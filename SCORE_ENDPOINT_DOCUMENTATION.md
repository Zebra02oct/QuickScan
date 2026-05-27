# Score Endpoint Documentation

## Overview
The Score endpoint calculates and returns a student's attendance score based on their presence records from the last 7 days. This endpoint supports both **class-only sessions** (`is_kelas_only=true`) and **subject-based sessions** (`is_kelas_only=false`).

## Endpoint Details

### Route
```
GET /api/v1/attendance/score
```

### Authentication
- **Type**: Bearer Token (Sanctum)
- **Required**: Yes
- **User Role**: `siswa` only

### Response Format

#### Success Response (200)
```json
{
  "score": 85.5,
  "tier": "Perak",
  "color": "#B4B4B4",
  "statistics": {
    "total_sessions": 4,
    "hadir": 3,
    "terlambat": 1,
    "izin": 0,
    "sakit": 0,
    "alpa": 0
  },
  "breakdown": {
    "class_only": {
      "hadir": 1,
      "terlambat": 0,
      "izin": 0,
      "sakit": 0,
      "alpa": 0
    },
    "mapel": {
      "hadir": 2,
      "terlambat": 1,
      "izin": 0,
      "sakit": 0,
      "alpa": 0
    }
  },
  "period": {
    "start_date": "2024-01-08",
    "end_date": "2024-01-15"
  }
}
```

#### Empty Response (No Data)
```json
{
  "score": 100,
  "tier": "Emas",
  "color": "#FFD700",
  "statistics": {
    "total_sessions": 0,
    "hadir": 0,
    "terlambat": 0,
    "izin": 0,
    "sakit": 0,
    "alpa": 0
  },
  "breakdown": {
    "class_only": {
      "hadir": 0,
      "terlambat": 0,
      "izin": 0,
      "sakit": 0,
      "alpa": 0
    },
    "mapel": {
      "hadir": 0,
      "terlambat": 0,
      "izin": 0,
      "sakit": 0,
      "alpa": 0
    }
  },
  "message": "Belum ada data presensi minggu ini"
}
```

#### Authorization Error (403)
```json
{
  "message": "Hanya akun siswa yang dapat mengakses skor presensi."
}
```

## Scoring System

### Score Calculation
- **Formula**: `(total_points / total_sessions)`
- **Hadir (Present)**: 100 points
- **Terlambat (Late)**: 50 points
- **Izin (Permitted Absence)**: 0 points
- **Sakit (Illness)**: 0 points
- **Alpa (Unexcused Absence)**: 0 points

### Score Tiers
| Score Range | Tier | Color | Badge |
|------------|------|-------|-------|
| ≥ 90 | Emas (Gold) | #FFD700 | 🥇 |
| 75-89 | Perak (Silver) | #B4B4B4 | 🥈 |
| < 75 | Perunggu (Bronze) | #CD7F32 | 🥉 |

## Data Breakdown

### `statistics`
Overall attendance statistics for the 7-day period:
- `total_sessions`: Total number of attendance sessions recorded
- `hadir`: Number of times student was present
- `terlambat`: Number of times student was late
- `izin`: Number of excused absences
- `sakit`: Number of illness-related absences
- `alpa`: Number of unexcused absences

### `breakdown`
Attendance statistics split by session type:
- **class_only**: Statistics for class-only sessions (`is_kelas_only=true`)
  - Sessions conducted by class homeroom teachers
  - Used for general class attendance tracking
  - Counted toward overall attendance score
- **mapel**: Statistics for subject-based sessions (`is_kelas_only=false`)
  - Sessions conducted by subject teachers
  - Used for subject-specific attendance tracking
  - Counted toward overall attendance score

## Period Information

The response includes the time period for which the score was calculated:
- `start_date`: 7 days before today (00:00:00)
- `end_date`: Today (23:59:59)

## Implementation Notes

### Class Attendance Support (Flutter)
The score endpoint now properly includes **class-only sessions** in score calculations. This ensures that:

1. ✅ Flutter mobile app displays complete attendance scores
2. ✅ Class homeroom sessions contribute to student's overall score
3. ✅ Breakdown statistics help students understand their attendance pattern
4. ✅ Both session types (class + subject) are counted equally in scoring

### Query Logic
```php
// Gets all attendance records for the student in the last 7 days
$absensi = Absensi::query()
    ->with('sesiAbsensi')
    ->where('siswa_id', $siswa->id)
    ->whereHas('sesiAbsensi', function ($q) use ($sevenDaysAgo, $today) {
        $q->whereBetween('tanggal', [$sevenDaysAgo, $today]);
    })
    ->get();

// Breakdown is determined by sesiAbsensi.is_kelas_only flag
$isKelasOnly = $a->sesiAbsensi?->is_kelas_only ?? false;
```

## Example Usage

### cURL Request
```bash
curl -X GET "https://api.example.com/api/v1/attendance/score" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Flutter Implementation
```dart
Future<AttendanceScore> getAttendanceScore() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/attendance/score'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    return AttendanceScore.fromJson(data);
  } else {
    throw Exception('Failed to fetch score');
  }
}

// Model
class AttendanceScore {
  final double score;
  final String tier;
  final String color;
  final Statistics statistics;
  final Breakdown breakdown;
  final Period period;

  AttendanceScore({
    required this.score,
    required this.tier,
    required this.color,
    required this.statistics,
    required this.breakdown,
    required this.period,
  });

  factory AttendanceScore.fromJson(Map<String, dynamic> json) {
    return AttendanceScore(
      score: (json['score'] ?? 100).toDouble(),
      tier: json['tier'] ?? 'Emas',
      color: json['color'] ?? '#FFD700',
      statistics: Statistics.fromJson(json['statistics'] ?? {}),
      breakdown: Breakdown.fromJson(json['breakdown'] ?? {}),
      period: Period.fromJson(json['period'] ?? {}),
    );
  }
}
```

## Related Endpoints

### `/api/v1/attendance/active`
Gets the currently active attendance session for the student
- Returns `is_kelas_only` flag to identify session type
- Used by mobile app for scanning QR codes

### `/api/v1/attendance/history`
Gets paginated attendance history
- Includes all attendance records (not just 7 days)
- Shows which type each session was (class or subject)
- Includes detailed session information

## Testing

Run the test script to verify score endpoint functionality:
```bash
php test_score_endpoint.php
```

This will:
1. ✅ Verify score calculation is correct
2. ✅ Check that class-only sessions are included
3. ✅ Check that mapel sessions are included
4. ✅ Display sample API response
5. ✅ Show breakdown statistics by session type

## Changelog

### v1.0.0 (Current)
- Added support for class-only sessions in score calculation
- Added detailed statistics breakdown by session type
- Added period information in response
- Enhanced Flutter app integration
- Improved response structure for better mobile app display

### v0.9.0 (Previous)
- Basic score calculation (subject-based only)
- Limited response structure
