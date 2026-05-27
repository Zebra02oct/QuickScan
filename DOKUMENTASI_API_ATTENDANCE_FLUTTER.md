# Dokumentasi API Attendance untuk Flutter

## Overview

API ini menyediakan endpoint untuk fitur presensi siswa termasuk history presensi dan scoring otomatis.

---

## 1. GET `/api/v1/attendance/history`

**Deskripsi**: Mengambil riwayat presensi siswa dengan pagination dan statistik.

**Headers**:

```
Authorization: Bearer {token}
```

**Query Parameters**:

```
per_page: integer (default: 15, max: 100)
```

**Response Success (200)**:

```json
{
    "data": [
        {
            "id": 1,
            "status": "hadir",
            "waktu_scan": "08:15:30",
            "keterangan": null,
            "tanggal": "2026-03-28",
            "mapel": "Bahasa Indonesia",
            "kelas": "10A",
            "guru": "Bu Siti"
        },
        {
            "id": 2,
            "status": "terlambat",
            "waktu_scan": "08:45:00",
            "keterangan": "Terjebak macet",
            "tanggal": "2026-03-27",
            "mapel": "Matematika",
            "kelas": "10A",
            "guru": "Pak Budi"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 50,
        "total": 234
    },
    "statistics": {
        "hadir": 150,
        "terlambat": 10,
        "izin": 20,
        "sakit": 10,
        "alpa": 44
    }
}
```

**Status Values**:

- `hadir` - Hadir
- `terlambat` - Terlambat
- `izin` - Izin
- `sakit` - Sakit
- `alpa` - Alpa

---

## 2. GET `/api/v1/attendance/score`

**Deskripsi**: Mengambil skor presensi siswa yang sudah dihitung di backend untuk 7 hari terakhir. ✅ **UPDATED**: Sekarang support presensi kelas (class attendance) beserta presensi mapel.

**Headers**:

```
Authorization: Bearer {token}
```

**Response Success (200)**:

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

**Scoring Formula** (Backend):

```
Total Points = (hadir × 100) + (terlambat × 50) + (lainnya × 0)
Final Score = Total Points ÷ Total Sessions

Periode Perhitungan: 7 hari terakhir (termasuk hari ini)
Termasuk: Presensi Kelas + Presensi Mapel
```

**Tier Classification**:
| Tier | Score | Warna |
|------|-------|-------|
| Emas | ≥ 90 | #FFD700 |
| Perak | ≥ 75 | #B4B4B4 |
| Perunggu | < 75 | #CD7F32 |

**Field Descriptions**:

- `score`: Skor presensi (0-100)
- `tier`: Tingkatan achievement (Emas/Perak/Perunggu)
- `color`: Warna untuk UI badge (#RGB)
- `statistics`: Statistik keseluruhan presensi
  - `total_sessions`: Total sesi presensi
  - `hadir`: Jumlah hadir
  - `terlambat`: Jumlah terlambat
  - `izin`: Jumlah izin
  - `sakit`: Jumlah sakit
  - `alpa`: Jumlah alpa
- `breakdown`: Pemecahan statistik berdasarkan tipe sesi
  - `class_only`: Presensi kelas (oleh wali kelas)
  - `mapel`: Presensi mapel (oleh guru mapel)
- `period`: Periode waktu perhitungan
  - `start_date`: Tanggal awal periode (7 hari lalu)
  - `end_date`: Tanggal akhir periode (hari ini)

---

## 3. Implementasi di Flutter

### Struktur Data Model

```dart
class AttendanceRecord {
  final int id;
  final String status;
  final String waktuScan;
  final String? keterangan;
  final String tanggal;
  final String? mapel;
  final String? kelas;
  final String? guru;
}

class AttendanceScore {
  final double score;
  final String tier;
  final String color;
  final AttendanceStatistics statistics;
  final AttendanceBreakdown breakdown;
  final AttendancePeriod period;

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
      score: (json['score'] as num).toDouble(),
      tier: json['tier'] ?? 'Emas',
      color: json['color'] ?? '#FFD700',
      statistics: AttendanceStatistics.fromJson(json['statistics'] ?? {}),
      breakdown: AttendanceBreakdown.fromJson(json['breakdown'] ?? {}),
      period: AttendancePeriod.fromJson(json['period'] ?? {}),
    );
  }
}

class AttendanceStatistics {
  final int totalSessions;
  final int hadir;
  final int terlambat;
  final int izin;
  final int sakit;
  final int alpa;

  AttendanceStatistics({
    required this.totalSessions,
    required this.hadir,
    required this.terlambat,
    required this.izin,
    required this.sakit,
    required this.alpa,
  });

  factory AttendanceStatistics.fromJson(Map<String, dynamic> json) {
    return AttendanceStatistics(
      totalSessions: json['total_sessions'] ?? 0,
      hadir: json['hadir'] ?? 0,
      terlambat: json['terlambat'] ?? 0,
      izin: json['izin'] ?? 0,
      sakit: json['sakit'] ?? 0,
      alpa: json['alpa'] ?? 0,
    );
  }
}

class AttendanceBreakdown {
  final Map<String, int> classOnly;  // Presensi Kelas
  final Map<String, int> mapel;      // Presensi Mapel

  AttendanceBreakdown({
    required this.classOnly,
    required this.mapel,
  });

  factory AttendanceBreakdown.fromJson(Map<String, dynamic> json) {
    return AttendanceBreakdown(
      classOnly: Map<String, int>.from(json['class_only'] ?? {}),
      mapel: Map<String, int>.from(json['mapel'] ?? {}),
    );
  }
}

class AttendancePeriod {
  final String startDate;
  final String endDate;

  AttendancePeriod({
    required this.startDate,
    required this.endDate,
  });

  factory AttendancePeriod.fromJson(Map<String, dynamic> json) {
    return AttendancePeriod(
      startDate: json['start_date'] ?? '',
      endDate: json['end_date'] ?? '',
    );
  }
}
```

### Langkah Implementasi

1. **Fetch History**

    ```
    GET /api/v1/attendance/history?per_page=50
    ```

    - Parse response ke list `AttendanceRecord`
    - Gunakan `meta` untuk pagination
    - Tampilkan statistik dari `statistics`

2. **Fetch Score** (UPDATED - Now with breakdown support)

    ```
    GET /api/v1/attendance/score
    ```

    - Parse response ke object `AttendanceScore` dengan semua fields
    - Gunakan `tier` dan `color` untuk styling UI badge
    - Tampilkan score dengan format: `{score.toStringAsFixed(2)}`
    - **NEW**: Gunakan breakdown untuk menampilkan analisis per tipe sesi:
      - `breakdown.classOnly`: Presensi kelas (Wali Kelas)
      - `breakdown.mapel`: Presensi Mapel (Guru Mata Pelajaran)
    - **NEW**: Tampilkan periode waktu (`period.startDate` - `period.endDate`)

3. **Error Handling**
    - 403 Forbidden: User bukan siswa
    - 401 Unauthorized: Token invalid/expired

### Contoh UI Display

```
┌─────────────────────────────────┐
│ 📊 SKOR PRESENSI (7 Hari)       │
├─────────────────────────────────┤
│                                 │
│  Skor: 85.50  [🥈 Perak]        │
│                                 │
│  Periode: 08 Jan - 15 Jan 2024  │
│                                 │
│  Total Sesi: 4                  │
│  ├─ Hadir: 3 (75%)              │
│  ├─ Terlambat: 1 (25%)          │
│  └─ Lainnya: 0                  │
│                                 │
│  Breakdown:                     │
│  ├─ Presensi Kelas:             │
│  │  └─ Hadir: 1                 │
│  └─ Presensi Mapel:             │
│     ├─ Hadir: 2                 │
│     └─ Terlambat: 1             │
│                                 │
└─────────────────────────────────┘
```

### Sample Code (Dart)

```dart
// Fetch score dari API
Future<AttendanceScore> fetchAttendanceScore() async {
  try {
    final response = await http.get(
      Uri.parse('$apiBaseUrl/api/v1/attendance/score'),
      headers: {
        'Authorization': 'Bearer $authToken',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return AttendanceScore.fromJson(data);
    } else if (response.statusCode == 403) {
      throw Exception('Hanya akun siswa yang dapat mengakses skor presensi');
    } else {
      throw Exception('Failed to load attendance score');
    }
  } catch (e) {
    throw Exception('Error: $e');
  }
}

// Display score dengan breakdown
Widget buildAttendanceScoreCard(AttendanceScore score) {
  final stats = score.statistics;
  final classOnlyTotal = score.breakdown.classOnly.values.reduce((a, b) => a + b);
  final mapelTotal = score.breakdown.mapel.values.reduce((a, b) => a + b);

  return Card(
    child: Column(
      children: [
        // Score Header
        Container(
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Color(int.parse(score.color.replaceFirst('#', '0xff'))),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Column(
            children: [
              Text(
                'Skor Presensi',
                style: TextStyle(fontSize: 14, color: Colors.white),
              ),
              SizedBox(height: 8),
              Text(
                score.score.toStringAsFixed(2),
                style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Colors.white),
              ),
              SizedBox(height: 8),
              Text(
                '${score.tier} (${score.period.startDate} - ${score.period.endDate})',
                style: TextStyle(fontSize: 12, color: Colors.white70),
              ),
            ],
          ),
        ),
        
        // Statistics
        Padding(
          padding: EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Total Sesi: ${stats.totalSessions}', style: TextStyle(fontWeight: FontWeight.bold)),
              SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _statItem('Hadir', stats.hadir, Colors.green),
                  _statItem('Terlambat', stats.terlambat, Colors.orange),
                  _statItem('Izin', stats.izin, Colors.blue),
                  _statItem('Sakit', stats.sakit, Colors.purple),
                  _statItem('Alpa', stats.alpa, Colors.red),
                ],
              ),
            ],
          ),
        ),

        // Breakdown
        Padding(
          padding: EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Breakdown:', style: TextStyle(fontWeight: FontWeight.bold)),
              SizedBox(height: 8),
              _breakdownSection('Presensi Kelas', score.breakdown.classOnly),
              SizedBox(height: 8),
              _breakdownSection('Presensi Mapel', score.breakdown.mapel),
            ],
          ),
        ),
      ],
    ),
  );
}
```

---

## 5. Notes Penting

✅ **Sudah Diimplementasi**:

- ✅ Endpoint history dengan pagination
- ✅ Endpoint score dengan tier dan color
- ✅ Statistics terpisah per status
- ✅ Backend calculation scoring
- ✅ **NEW**: Class attendance support (Presensi Kelas)
- ✅ **NEW**: Breakdown statistics per tipe sesi (class_only vs mapel)
- ✅ **NEW**: Period information (tanggal mulai - tanggal akhir)
- ✅ **NEW**: Detailed response structure untuk Flutter

🔄 **Class Attendance (Presensi Kelas) Integration**:

- Sesi kelas (adalah_kelas_only=true) kini dimasukkan dalam perhitungan skor
- Presensi kelas dijumlahkan bersama presensi mapel
- Flutter dapat menampilkan breakdown untuk analisis lebih detail
- Baik presensi kelas maupun mapel memiliki bobot yang sama dalam scoring

❌ **Fallback (Jika diperlukan)**:

- Jika endpoint score gagal, Flutter bisa calculate local menggunakan data dari history
- Gunakan statistics field untuk fallback calculation

---

## 6. Testing

**Curl Command** - History:

```bash
curl -X GET "http://localhost:8000/api/v1/attendance/history?per_page=50" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Curl Command** - Score (Updated with class attendance):

```bash
curl -X GET "http://localhost:8000/api/v1/attendance/score" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Example Response** (Score endpoint dengan class attendance):

```bash
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

**Run Test Script**:

```bash
php test_score_endpoint.php
```

---

**Last Updated**: May 18, 2026  
**Status**: Ready for Flutter Integration
