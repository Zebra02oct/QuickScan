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

**Deskripsi**: Mengambil skor presensi siswa yang sudah dihitung di backend.

**Headers**:

```
Authorization: Bearer {token}
```

**Response Success (200)**:

```json
{
    "score": 87.5,
    "tier": "Perak",
    "color": "#B4B4B4"
}
```

**Scoring Formula** (Backend):

```
Total Points = (hadir × 100) + (terlambat × 50) + (lainnya × 0)
Final Score = Total Points ÷ Total Sessions
```

**Tier Classification**:
| Tier | Score | Warna |
|------|-------|-------|
| Emas | ≥ 90 | #FFD700 |
| Perak | ≥ 75 | #B4B4B4 |
| Perunggu | < 75 | #CD7F32 |

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
}

class AttendanceStatistics {
  final int hadir;
  final int terlambat;
  final int izin;
  final int sakit;
  final int alpa;
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

2. **Fetch Score**

    ```
    GET /api/v1/attendance/score
    ```

    - Parse response ke object `AttendanceScore`
    - Gunakan `tier` dan `color` untuk styling UI
    - Tampilkan score dengan desimal 2 angka (contoh: 87.50)

3. **Error Handling**
    - 403 Forbidden: User bukan siswa
    - 401 Unauthorized: Token invalid/expired

---

## 4. Notes Penting

✅ **Sudah Diimplementasi**:

- ✅ Endpoint history dengan pagination
- ✅ Endpoint score dengan tier dan color
- ✅ Statistics terpisah per status
- ✅ Backend calculation scoring

❌ **Fallback (Jika diperlukan)**:

- Jika endpoint score gagal, Flutter bisa calculate local menggunakan data dari history

---

## 5. Testing

**Curl Command**:

```bash
# History
curl -X GET "http://localhost:8000/api/v1/attendance/history?per_page=50" \
  -H "Authorization: Bearer {token}"

# Score
curl -X GET "http://localhost:8000/api/v1/attendance/score" \
  -H "Authorization: Bearer {token}"
```

---

**Last Updated**: May 18, 2026  
**Status**: Ready for Flutter Integration
