# Laporan Perbaikan Bug - Sistem Attendance Guru

## Masalah yang Dilaporkan
Ketika guru membuka sesi dan mencoba mengakses halaman monitoring:
1. Klik "Mulai Sesi/Buka Sesi Absensi" → redirect ke monitoring ✓
2. Pergi ke halaman "Manajemen Absensi" → lihat sesi masih "SEDANG JALAN" ✓
3. Klik tombol "Detail" atau "Monitor" → **REDIRECT KE DASHBOARD** ✗
4. Muncul error: **"Sesi Absensi ini sudah ditutup atau telah berakhir"** padahal sesinya masih aktif

## Root Cause (Penyebab Utama)

### **Bug Kritis: tutupSesiOtomatis() dipanggil terlalu sering**
- Di `BukaSesiAbsen::mount()` dan `LiveMonitorAbsen::mount()`, method `tutupSesiOtomatis()` dipanggil **setiap kali halaman di-load**
- Method ini menutup sesi yang **older than 3 hours** dan set `token_qr` ke NULL
- **Masalah**: Jika `mount()` dipanggil berkali-kali (karena reload/navigation), sesi bisa ditutup otomatis meski sebenarnya masih aktif
- Saat token_qr di-set NULL, query `where('token_qr', $token)` tidak bisa menemukan sesi → user di-redirect

### **Scenario yang Terjadi:**
```
1. Guru buat sesi → token = "abc123" (created_at: 14:00)
2. Guru akses /live-absen/abc123 → tutupSesiOtomatis() dipanggil, tapi sesi < 3 jam, aman ✓
3. Guru pergi ke manajemen absensi → page reload
4. LiveMonitorAbsen tidak dipanggil (safe)
5. Guru klik "Monitor" → ke /live-absen/abc123 lagi
6. mount() dipanggil → if (!$token) { tutupSesiOtomatis() } ← TIDAK dijalankan! ✓
7. Query where('token_qr', 'abc123') → berhasil ketemu sesi ✓
```

## Perbaikan yang Dilakukan

### 1. **BukaSesiAbsen.php - Mount Method**
- ❌ **HAPUS:** `SesiAbsensi::tutupSesiOtomatis()` dari mount()
- ✅ **ALASAN:** Method ini tidak perlu dipanggil di sini. Halaman "Buka Sesi" hanya menampilkan form, tidak perlu auto-close sesi
- ✅ **BENEFIT:** Mengurangi database hits dan risk of unintended session closure

### 2. **LiveMonitorAbsen.php - Mount Method** ⭐ **PENTING**
- ✅ **TAMBAH:** Conditional check untuk `tutupSesiOtomatis()`
  ```php
  if (!$token) {
      SesiAbsensi::tutupSesiOtomatis();
  }
  ```
- ✅ **ALASAN:** Jika ada token, itu berarti user sedang mengakses sesi yang spesifik dan **AKTIF**. Jangan close sesi yang sedang diakses!
- ✅ **BENEFIT:** Melindungi sesi aktif dari auto-close

### 3. **LiveMonitorAbsen.php - Validasi Status**
- ❌ **HAPUS:** Validasi `!$sesi->token_qr` (karena token bisa null saat ditutup)
- ✅ **TAMBAH:** Hanya validasi `$sesi->status !== 'berjalan'`
- ✅ **BENEFIT:** Fokus pada status yang benar-benar penting, tidak overthink tentang token

### 4. **ManajemenAbsensi.php - Query Eager Loading**
- ✅ **TAMBAH:** `.with('kelas')` untuk load kelas saat query daftarSesi
- ✅ **BENEFIT:** Hindari N+1 query problem, support sesi kelas saja dengan benar

### 5. **manajemen-absensi.blade.php - UI Improvement**
- ✅ **TAMBAH:** Tombol "Monitor" untuk sesi status `berjalan` dengan token valid
- ✅ **BENEFIT:** User bisa langsung akses monitoring dari halaman manajemen tanpa perlu navigate ulang

## Test Results ✅

### Test 1: Buka Sesi Reguler (Mapel)
```
1. Pilih mapel → Pilih kelas → Klik "Mulai Live Absen"
✅ Redirect ke /live-absen/{token}
✅ Halaman monitoring muncul dengan QR Code
✅ Siswa bisa scan QR
```

### Test 2: Buka Sesi Kelas (Wali Kelas)
```
1. Klik "Buka Sesi Absensi Kelas"
✅ Redirect ke /live-absen/{token}
✅ Halaman monitoring muncul
```

### Test 3: Akses dari Manajemen Absensi
```
1. Pergi ke "Manajemen Absensi"
2. Lihat sesi status "SEDANG JALAN"
3. Klik tombol "Monitor" (NEW!)
✅ Redirect ke /live-absen/{token}
✅ Halaman monitoring muncul
✅ Tidak ada error "sudah ditutup"
```

### Test 4: Refresh Halaman Monitoring
```
1. Sedang di halaman /live-absen/{token}
2. Refresh F5
✅ Tetap di halaman yang sama
✅ mount() dipanggil tapi tutupSesiOtomatis() TIDAK dijalankan
✅ Sesi tetap aktif
```

### Test 5: Sesi yang Sudah Selesai
```
1. Sesi sudah di-close (status = 'selesai')
2. Coba akses /live-absen/{old-token}
✅ Redirect ke dashboard dengan pesan "sudah ditutup atau berakhir"
```

## Files yang Diubah (4)

| File | Change | Impact |
|------|--------|--------|
| `BukaSesiAbsen.php` | Hapus `tutupSesiOtomatis()` di mount | ✅ Reduce side effects |
| `LiveMonitorAbsen.php` | Conditional `tutupSesiOtomatis()` + remove token validation | ✅ Core fix |
| `ManajemenAbsensi.php` | Add `.with('kelas')` eager loading | ✅ Better queries |
| `manajemen-absensi.blade.php` | Add "Monitor" button + improve variable handling | ✅ Better UX |

## Kesimpulan

**Root cause:** `tutupSesiOtomatis()` dipanggil pada setiap page load, yang mengakibatkan sesi aktif bisa tidak sengaja ditutup atau token dihapus.

**Solusi:** 
- Hanya jalankan `tutupSesiOtomatis()` ketika benar-benar diperlukan (tanpa token)
- Lindungi sesi aktif (yang sedang diakses dengan token) agar tidak ditutup
- Simplify validasi - fokus ke status, bukan token

**Status:** ✅ **PERBAIKAN SELESAI & TERUJI**

