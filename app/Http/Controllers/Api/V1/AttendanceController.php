<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiAbsensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    private function isDuplicateAbsensi(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'Duplicate entry')
            && str_contains($message, 'absensis_sesi_absensi_id_siswa_id_unique');
    }

    private function ensureActiveStudent(Request $request): ?JsonResponse
    {
        $user = $request->user()->loadMissing('siswa');

        if ($user->role !== 'siswa' || ! $user->siswa) {
            return response()->json([
                'message' => 'Hanya akun siswa yang dapat mengakses fitur ini.',
            ], 403);
        }

        if ($user->status !== 'aktif') {
            return response()->json([
                'message' => 'Akun siswa tidak aktif.',
            ], 403);
        }

        return null;
    }

    public function scan(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'qr_token' => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () {
            SesiAbsensi::tutupSesiOtomatis();
        });

        if ($response = $this->ensureActiveStudent($request)) {
            return $response;
        }

        $user = $request->user()->loadMissing('siswa');

        $siswa = $user->siswa;

        // Cari sesi berdasarkan token
        $sesiAktif = SesiAbsensi::where('token_qr', '=', $payload['qr_token'])
            ->where('status', 'berjalan')
            ->first();

        if (!$sesiAktif) {
            return response()->json([
                'message' => 'QR Code tidak valid atau sesi sudah ditutup oleh guru.',
            ], 400);
        }

        $sesiAktif->load(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user']);

        if (! $sesiAktif->guruMapel) {
            return response()->json([
                'message' => 'Data sesi tidak lengkap.',
            ], 422);
        }

        if ((int) $sesiAktif->guruMapel->kelas_id !== (int) $siswa->kelas_id) {
            return response()->json([
                'message' => 'Sesi absen ini bukan untuk kelas Anda.',
            ], 403);
        }

        try {
            $absensi = Absensi::create([
                'sesi_absensi_id' => $sesiAktif->id,
                'siswa_id' => $siswa->id,
                'waktu_scan' => now()->toTimeString(),
                'status' => 'hadir',
            ]);
        } catch (QueryException $e) {
            if ($this->isDuplicateAbsensi($e)) {
                return response()->json([
                    'message' => 'Anda sudah absen pada sesi ini.',
                ], 409);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Berhasil! Kehadiran tercatat.',
            'data' => [
                'absensi_id' => $absensi->id,
                'sesi_absensi_id' => $sesiAktif->id,
                'status' => $absensi->status,
                'attendance_status' => ucfirst($absensi->status),
                'waktu_scan' => $absensi->waktu_scan,
                'tanggal' => optional($sesiAktif->tanggal)->toDateString(),
                'mapel' => $sesiAktif->guruMapel?->mapel?->nama_mapel,
                'kelas' => $sesiAktif->guruMapel?->kelas?->nama_kelas,
                'guru' => $sesiAktif->guruMapel?->guru?->user?->name,
            ],
        ], 201);
    }

    public function history(Request $request): JsonResponse
    {
        if ($response = $this->ensureActiveStudent($request)) {
            return $response;
        }

        $user = $request->user()->loadMissing('siswa');

        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = Absensi::with([
            'sesiAbsensi.guruMapel.mapel',
            'sesiAbsensi.guruMapel.kelas',
            'sesiAbsensi.guruMapel.guru.user',
        ])->where('siswa_id', $user->siswa->id)->latest();

        $paginated = $query->paginate($perPage);

        $allStatsQuery = Absensi::query()->where('siswa_id', $user->siswa->id);
        $allStats = $allStatsQuery->get();

        return response()->json([
            'data' => collect($paginated->items())->map(function ($absen) {
                return [
                    'id' => $absen->id,
                    'status' => $absen->status,
                    'waktu_scan' => $absen->waktu_scan,
                    'keterangan' => $absen->keterangan,
                    'tanggal' => optional($absen->sesiAbsensi?->tanggal)->toDateString(),
                    'mapel' => $absen->sesiAbsensi?->guruMapel?->mapel?->nama_mapel,
                    'kelas' => $absen->sesiAbsensi?->guruMapel?->kelas?->nama_kelas,
                    'guru' => $absen->sesiAbsensi?->guruMapel?->guru?->user?->name,
                ];
            })->values(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
            'statistics' => [
                'hadir' => $allStats->where('status', 'hadir')->count(),
                'izin' => $allStats->where('status', 'izin')->count(),
                'sakit' => $allStats->where('status', 'sakit')->count(),
                'alpa' => $allStats->where('status', 'alpa')->count(),
            ],
        ]);
    }

    public function score(Request $request): JsonResponse
    {
        if ($response = $this->ensureActiveStudent($request)) {
            return $response;
        }
        $user = $request->user()->loadMissing('siswa');
        $siswa = $user->siswa;
        $sevenDaysAgo = now()->subDays(7)->startOfDay();
        $today = now()->endOfDay();
        $totalSessions = \App\Models\SesiAbsensi::whereHas('guruMapel', function ($q) use ($siswa) {
            $q->where('kelas_id', $siswa->kelas_id);
        })
            ->whereBetween('tanggal', [$sevenDaysAgo, $today])
            ->count();
        if ($totalSessions === 0) {
            return response()->json([
                'score' => 100,
                'tier' => 'Emas',
                'color' => '#FFD700',
            ]);
        }
        $absensi = Absensi::query()
            ->where('siswa_id', $siswa->id)
            ->whereHas('sesiAbsensi', function ($q) use ($sevenDaysAgo, $today) {
                $q->whereBetween('tanggal', [$sevenDaysAgo, $today]);
            })
            ->get();

        $totalPoints = 0;

        foreach ($absensi as $a) {
            $totalPoints += match ($a->status) {
                'hadir' => 100,
                'terlambat' => 50,
                default => 0,
            };
        }
        $score = ($totalPoints / $totalSessions);
        if ($score >= 90) {
            $tier = 'Imortal';
            $color = '#FFD700';
        } elseif ($score >= 75) {
            $tier = 'Diamond';
            $color = '#B4B4B4';
        } else {
            $tier = 'Bronze';
            $color = '#CD7F32';
        }

        return response()->json([
            'score' => round($score, 2),
            'tier' => $tier,
            'color' => $color,
        ]);
    }

    public function active(Request $request): JsonResponse
    {
        SesiAbsensi::tutupSesiOtomatis();
        if ($response = $this->ensureActiveStudent($request)) {
            return $response;
        }
        $user = $request->user()->loadMissing('siswa');
        $siswa = $user->siswa;
        $sesiQuery = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user'])
            ->where('status', 'berjalan')
            ->whereHas('guruMapel', function ($q) use ($siswa) {
                $q->where('kelas_id', $siswa->kelas_id);
            });
        $sesi = $sesiQuery->latest()->first();
        if (!$sesi) {
            return response()->json([
                'active_session' => null,
            ]);
        }

        return response()->json([
            'active_session' => [
                'sesi_absensi_id' => $sesi->id,
                'qr_token' => $sesi->token_qr,
                'status' => $sesi->status,
                'tanggal' => optional($sesi->tanggal)->toDateString(),
                'waktu_mulai' => $sesi->waktu_mulai,
                'estimated_close' => optional($sesi->created_at)?->copy()->addHours(3)?->toDateTimeString(),
                'mapel' => $sesi->guruMapel?->mapel?->nama_mapel,
                'kelas' => $sesi->guruMapel?->kelas?->nama_kelas,
                'guru' => $sesi->guruMapel?->guru?->user?->name,
            ],
        ]);
    }
}