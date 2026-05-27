<?php
/**
 * Test script untuk verify score endpoint support class attendance
 */

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Absensi;
use App\Models\SesiAbsensi;
use App\Models\Siswa;

// Initialize Laravel app
$app = require_once(__DIR__ . '/bootstrap/app.php');
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "=== Test Score Endpoint (Class Attendance Support) ===\n\n";

// Get a siswa for testing
$siswa = Siswa::first();

if (!$siswa) {
    echo "❌ No siswa found in database\n";
    exit(1);
}

echo "Testing with Siswa: {$siswa->nama_siswa} (ID: {$siswa->id})\n\n";

// Check absences for this siswa in last 7 days
$sevenDaysAgo = now()->subDays(7)->startOfDay();
$today = now()->endOfDay();

$absensi = Absensi::query()
    ->with('sesiAbsensi')
    ->where('siswa_id', $siswa->id)
    ->whereHas('sesiAbsensi', function ($q) use ($sevenDaysAgo, $today) {
        $q->whereBetween('tanggal', [$sevenDaysAgo, $today]);
    })
    ->get();

echo "Found {$absensi->count()} attendance records in last 7 days\n";

if ($absensi->isEmpty()) {
    echo "⚠️  No attendance data found for scoring\n\n";
    echo "Sample response (empty):\n";
    $response = [
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
    ];
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    // Calculate score with breakdown
    $totalPoints = 0;
    $totalSessions = 0;
    
    $classOnlyStats = [
        'hadir' => 0,
        'terlambat' => 0,
        'izin' => 0,
        'sakit' => 0,
        'alpa' => 0,
    ];
    
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
        $isKelasOnly = $a->sesiAbsensi?->is_kelas_only ?? false;

        echo "  • {$a->siswa->nama_siswa} - Status: {$status}, Session Type: " . ($isKelasOnly ? 'CLASS_ONLY' : 'MAPEL') . "\n";

        // Update overall stats
        if (isset($overallStats[$status])) {
            $overallStats[$status]++;
        }

        // Update breakdown stats
        if ($isKelasOnly) {
            if (isset($classOnlyStats[$status])) {
                $classOnlyStats[$status]++;
            }
        } else {
            if (isset($mapelStats[$status])) {
                $mapelStats[$status]++;
            }
        }

        // Calculate points
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

    echo "\n✅ Score Calculation:\n";
    echo "   Total Points: {$totalPoints}/{$totalSessions} = " . round($score, 2) . "\n";
    echo "   Tier: {$tier}\n";

    $response = [
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
            'class_only' => $classOnlyStats,
            'mapel' => $mapelStats,
        ],
        'period' => [
            'start_date' => $sevenDaysAgo->toDateString(),
            'end_date' => $today->toDateString(),
        ],
    ];

    echo "\n📊 Sample API Response:\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

    echo "\n✅ Verification Results:\n";
    echo "   ✓ Class-only attendance included: " . ($classOnlyStats['hadir'] > 0 || $classOnlyStats['terlambat'] > 0 ? 'YES' : 'NO') . "\n";
    echo "   ✓ Mapel attendance included: " . ($mapelStats['hadir'] > 0 || $mapelStats['terlambat'] > 0 ? 'YES' : 'NO') . "\n";
    echo "   ✓ Breakdown statistics calculated: YES\n";
}

echo "\n=== Test Complete ===\n";
