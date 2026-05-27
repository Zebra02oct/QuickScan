<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function scan(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'qr_token' => [
                'required',
                'string',
                'size:10',
                'regex:/^[a-zA-Z0-9]{10}$/'
            ],
        ]);

        SesiAbsensi::tutupSesiOtomatis();

        $user = $request->user()->loadMissing('siswa');

        if ($user->role !== 'siswa' || ! $user->siswa) {
            return response()->json([
                'message' => 'Hanya akun siswa yang dapat melakukan scan.',
            ], 403);
        }

        $siswa = $user->siswa;
        $sesiAktif = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user'])
            ->where('token_qr', $payload['qr_token'])
            ->where('status', 'berjalan')
            ->whereHas('guruMapel', function ($query) use ($siswa) {
                $query->where('kelas_id', $siswa->kelas_id);
            })
            ->first();

        if (!$sesiAktif) {
            return response()->json([
                'message' => 'QR Code tidak valid atau sesi sudah ditutup oleh guru.',
            ], 400);
        }
        $sesiStartTime = Carbon::parse($sesiAktif->created_at);
        $sesiEndTime = $sesiStartTime->copy()->addHours(3);
        $currentTime = now();

        if ($currentTime < $sesiStartTime) {
            return response()->json([
                'message' => 'Sesi absensi belum dimulai.',
            ], 400);
        }

        if ($currentTime > $sesiEndTime) {
            return response()->json([
                'message' => 'Sesi absensi sudah ditutup (melewati 3 jam).',
            ], 400);
        }
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
            'waktu_scan' => $currentTime->toTimeString(),
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
        $user = $request->user()->loadMissing('siswa');

        if ($user->role !== 'siswa' || ! $user->siswa) {
            return response()->json([
                'message' => 'Hanya akun siswa yang dapat mengakses skor presensi.',
            ], 403);
        }
        $siswa = $user->siswa;
        $sevenDaysAgo = now()->subDays(7)->startOfDay();
        $today = now()->endOfDay();

        $absensi = Absensi::query()
            ->with('sesiAbsensi')
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
                'statistics' => [
                    'total_sessions' => 0,
                    'hadir' => 0,
                    'terlambat' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpa' => 0,
                ],
                'breakdown' => [
                    'class_only' => [
                        'hadir' => 0,
                        'terlambat' => 0,
                        'izin' => 0,
                        'sakit' => 0,
                        'alpa' => 0,
                    ],
                    'mapel' => [
                        'hadir' => 0,
                        'terlambat' => 0,
                        'izin' => 0,
                        'sakit' => 0,
                        'alpa' => 0,
                    ],
                ],
                'message' => 'Belum ada data presensi minggu ini',
            ]);
        }
        $totalPoints = 0;
        $totalSessions = 0;
        $mapelStats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];

        $overallStats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];
        foreach ($absensi as $a) {
            $totalSessions++;
            $status = $a->status;
            if (isset($overallStats[$status])) {
                $overallStats[$status]++;
            }
            if (isset($mapelStats[$status])) {
                $mapelStats[$status]++;
            }
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
            'statistics' => [
                'total_sessions' => $totalSessions,
                'hadir' => $overallStats['hadir'],
                'terlambat' => $overallStats['terlambat'],
                'izin' => $overallStats['izin'],
                'sakit' => $overallStats['sakit'],
                'alpa' => $overallStats['alpa'],
            ],
            'breakdown' => [
                'mapel' => $mapelStats,
            ],
            'period' => [
                'start_date' => $sevenDaysAgo->toDateString(),
                'end_date' => $today->toDateString(),
            ],
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

        $sesiQuery = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user'])
            ->where('status', 'berjalan')
            ->whereHas('guruMapel', function ($query) use ($siswa) {
                $query->where('kelas_id', $siswa->kelas_id);
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
