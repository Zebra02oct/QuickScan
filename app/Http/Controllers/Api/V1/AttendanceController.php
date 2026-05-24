<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\SesiAbsensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function scan(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'qr_token' => ['required', 'string', 'max:100'],
        ]);

        SesiAbsensi::tutupSesiOtomatis();

        $user = $request->user()->loadMissing('siswa');

        if ($user->role !== 'siswa' || ! $user->siswa) {
            return response()->json([
                'message' => 'Hanya akun siswa yang dapat melakukan scan.',
            ], 403);
        }

        $siswa = $user->siswa;

        // Cari sesi berdasarkan token
        $sesiAktif = SesiAbsensi::where('token_qr', $payload['qr_token'])
            ->where('status', 'berjalan')
            ->first();

        if (!$sesiAktif) {
            return response()->json([
                'message' => 'QR Code tidak valid atau sesi sudah ditutup oleh guru.',
            ], 400);
        }

        // Cek apakah sesi kelas saja (tanpa mapel)
        if ($sesiAktif->is_kelas_only) {
            $sesiAktif->load(['kelas.waliKelas.user']);

            // Untuk sesi kelas saja, cek apakah siswa ada di kelas yang sesuai
            $kelasSesii = $sesiAktif->kelas;

            if (!$kelasSesii || (int) $kelasSesii->id !== (int) $siswa->kelas_id) {
                return response()->json([
                    'message' => 'Sesi absen ini bukan untuk kelas Anda.',
                ], 403);
            }

            $sudahAbsen = Absensi::query()->where('sesi_absensi_id', $sesiAktif->id)
                ->where('siswa_id', $siswa->id)
                ->exists();

            if ($sudahAbsen) {
                return response()->json([
                    'message' => 'Anda sudah absen pada sesi ini.',
                ], 409);
            }

            $absensi = Absensi::create([
                'sesi_absensi_id' => $sesiAktif->id,
                'siswa_id' => $siswa->id,
                'waktu_scan' => now()->toTimeString(),
                'status' => 'hadir',
            ]);

            return response()->json([
                'message' => 'Berhasil! Kehadiran tercatat.',
                'data' => [
                    'absensi_id' => $absensi->id,
                    'sesi_absensi_id' => $sesiAktif->id,
                    'status' => $absensi->status,
                    'attendance_status' => ucfirst($absensi->status),
                    'waktu_scan' => $absensi->waktu_scan,
                    'tanggal' => optional($sesiAktif->tanggal)->toDateString(),
                    'mapel' => 'Absensi Kelas',
                    'kelas' => $kelasSesii->nama_kelas,
                    'guru' => $sesiAktif->kelas?->waliKelas?->user?->name ?? '-',
                ],
            ], 201);
        }

        // Sesi reguler (dengan mapel)
        $sesiAktif->load(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user']);

        if ((int) $sesiAktif->guruMapel->kelas_id !== (int) $siswa->kelas_id) {
            return response()->json([
                'message' => 'Sesi absen ini bukan untuk kelas Anda.',
            ], 403);
        }

        $sudahAbsen = Absensi::query()->where('sesi_absensi_id', $sesiAktif->id)
            ->where('siswa_id', $siswa->id)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'message' => 'Anda sudah absen pada sesi ini.',
            ], 409);
        }

        $absensi = Absensi::create([
            'sesi_absensi_id' => $sesiAktif->id,
            'siswa_id' => $siswa->id,
            'waktu_scan' => now()->toTimeString(),
            'status' => 'hadir',
        ]);

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
        $user = $request->user()->loadMissing('siswa');

        if ($user->role !== 'siswa' || ! $user->siswa) {
            return response()->json([
                'message' => 'Hanya akun siswa yang dapat mengakses riwayat absensi.',
            ], 403);
        }

        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = Absensi::with([
            'sesiAbsensi.guruMapel.mapel',
            'sesiAbsensi.guruMapel.kelas',
            'sesiAbsensi.guruMapel.guru.user',
            'sesiAbsensi.kelas.waliKelas.user',
        ])->where('siswa_id', $user->siswa->id)->latest();

        $paginated = $query->paginate($perPage);

        $allStatsQuery = Absensi::query()->where('siswa_id', $user->siswa->id);
        $allStats = $allStatsQuery->get();

        return response()->json([
            'data' => collect($paginated->items())->map(function ($absen) {
                $mapel = $absen->sesiAbsensi?->is_kelas_only
                    ? 'Absensi Kelas'
                    : $absen->sesiAbsensi?->guruMapel?->mapel?->nama_mapel;

                $kelas = $absen->sesiAbsensi?->is_kelas_only
                    ? $absen->sesiAbsensi?->kelas?->nama_kelas
                    : $absen->sesiAbsensi?->guruMapel?->kelas?->nama_kelas;

                $guru = $absen->sesiAbsensi?->is_kelas_only
                    ? ($absen->sesiAbsensi?->kelas?->waliKelas?->user?->name ?? '-')
                    : $absen->sesiAbsensi?->guruMapel?->guru?->user?->name;

                return [
                    'id' => $absen->id,
                    'status' => $absen->status,
                    'waktu_scan' => $absen->waktu_scan,
                    'keterangan' => $absen->keterangan,
                    'tanggal' => optional($absen->sesiAbsensi?->tanggal)->toDateString(),
                    'mapel' => $mapel,
                    'kelas' => $kelas,
                    'guru' => $guru,
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
        $user = $request->user()->loadMissing('siswa');

        if ($user->role !== 'siswa' || ! $user->siswa) {
            return response()->json([
                'message' => 'Hanya akun siswa yang dapat mengakses skor presensi.',
            ], 403);
        }

        $siswa = $user->siswa;

        // Ambil data 7 hari terakhir (minggu terbaru)
        $sevenDaysAgo = now()->subDays(7)->startOfDay();
        $today = now()->endOfDay();

        $absensi = Absensi::query()
            ->where('siswa_id', $siswa->id)
            ->whereHas('sesiAbsensi', function ($q) use ($sevenDaysAgo, $today) {
                $q->whereBetween('tanggal', [$sevenDaysAgo, $today]);
            })
            ->get();

        if ($absensi->isEmpty()) {
            return response()->json([
                'score' => 100,
                'tier' => 'Emas',
                'color' => '#FFD700',
            ]);
        }

        $totalPoints = 0;
        $totalSessions = 0;

        foreach ($absensi as $a) {
            $totalSessions++;

            $totalPoints += match ($a->status) {
                'hadir' => 100,
                'terlambat' => 50,
                default => 0,
            };
        }

        $score = ($totalPoints / $totalSessions);

        if ($score >= 90) {
            $tier = 'Emas';
            $color = '#FFD700';
        } elseif ($score >= 75) {
            $tier = 'Perak';
            $color = '#B4B4B4';
        } else {
            $tier = 'Perunggu';
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

        $user = $request->user()->loadMissing('siswa');

        if ($user->role !== 'siswa' || ! $user->siswa) {
            return response()->json([
                'message' => 'Hanya akun siswa yang dapat mengakses sesi aktif.',
            ], 403);
        }

        $siswa = $user->siswa;

        // Cari sesi aktif yang sesuai dengan kelas siswa
        $sesiQuery = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user', 'kelas.waliKelas.user'])
            ->where('status', 'berjalan')
            ->where(function ($query) use ($siswa) {
                // Sesi reguler - cocok dengan kelas siswa
                $query->where('is_kelas_only', false)
                    ->whereHas('guruMapel', function ($q) use ($siswa) {
                        $q->where('kelas_id', $siswa->kelas_id);
                    });

                // Sesi kelas saja - siswa bisa scan jika ada sesi kelas untuk kelasnya
                $query->orWhere(function ($q) use ($siswa) {
                    $q->where('is_kelas_only', true)
                        ->where('kelas_id', $siswa->kelas_id);
                });
            });

        $sesi = $sesiQuery->latest()->first();

        if (!$sesi) {
            return response()->json([
                'active_session' => null,
            ]);
        }

        $mapel = $sesi->is_kelas_only
            ? 'Absensi Kelas'
            : $sesi->guruMapel?->mapel?->nama_mapel;

        $kelasNama = $sesi->is_kelas_only
            ? ($sesi->kelas?->nama_kelas ?? '-')
            : $sesi->guruMapel?->kelas?->nama_kelas;

        $guru = $sesi->is_kelas_only
            ? ($sesi->kelas?->waliKelas?->user?->name ?? '-')
            : $sesi->guruMapel?->guru?->user?->name;

        return response()->json([
            'active_session' => [
                'sesi_absensi_id' => $sesi->id,
                'qr_token' => $sesi->token_qr,
                'status' => $sesi->status,
                'tanggal' => optional($sesi->tanggal)->toDateString(),
                'waktu_mulai' => $sesi->waktu_mulai,
                'estimated_close' => optional($sesi->created_at)?->copy()->addHours(3)?->toDateTimeString(),
                'mapel' => $mapel,
                'kelas' => $kelasNama,
                'guru' => $guru,
                'is_kelas_only' => $sesi->is_kelas_only ?? false,
            ],
        ]);
    }
}