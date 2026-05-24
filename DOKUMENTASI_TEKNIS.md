# Dokumentasi Teknis ProjectAbsensiSMK

## 1. Ringkasan Proyek

ProjectAbsensiSMK adalah aplikasi web absensi sekolah berbasis Laravel + Livewire dengan tiga peran utama:

- Admin: manajemen master data dan monitoring global absensi.
- Guru: membuka sesi absensi, menampilkan QR, monitor live, menutup sesi.
- Siswa: scan QR untuk mencatat kehadiran dan melihat riwayat.

Catatan penting integrasi mobile:

- Saat ini aplikasi berjalan sebagai web app berbasis session (bukan REST API).
- API mobile sudah tersedia di routes/api.php dengan prefix /api/v1.
- Integrasi Flutter sangat disarankan melalui penambahan layer API khusus mobile.

## 2. Stack Teknologi

### 2.1 Backend

- PHP: ^8.2
- Framework: Laravel 12
- Realtime UI server interaction: Livewire 3
- Package QR: simplesoftwareio/simple-qrcode
- Import/export Excel: maatwebsite/excel

### 2.2 Frontend

- Blade + Livewire
- Tailwind CSS
- Vite
- html5-qrcode (di sisi browser siswa)

### 2.3 Build dan Dev Script

- composer setup:
    - composer install
    - copy .env
    - php artisan key:generate
    - php artisan migrate --force
    - npm install
    - npm run build
- composer dev menjalankan:
    - php artisan serve
    - php artisan queue:listen
    - php artisan pail
    - npm run dev

## 3. Arsitektur Aplikasi

### 3.1 Pola Arsitektur

Aplikasi menggunakan pola:

- Routing web -> Livewire component
- Livewire component -> Eloquent model
- Blade view untuk rendering + event Livewire

Tidak ada controller CRUD utama untuk fitur inti absensi; sebagian besar logic berada di komponen Livewire.

### 3.2 Role Based Access Control

- Middleware auth + role digunakan pada route group.
- Alias middleware role didaftarkan ke App\Http\Middleware\CheckRole.
- Rule role:
    - admin: akses /admin/\*
    - guru: akses /guru/\*
    - siswa: akses /siswa/\*

### 3.3 Session/Auth

- Auth menggunakan guard web (session cookie).
- Login via email + password.
- Logout menginvalidasi session dan meregenerasi CSRF token.

## 4. Daftar Route Utama

## 4.1 Public + Auth

- GET / -> welcome
- GET /login -> Livewire Auth Login
- GET /forgot-password -> Livewire ForgotPassword
- GET /reset-password/{token} -> Livewire ResetPassword
- POST /logout -> logout session
- GET /dashboard -> redirect by role

## 4.2 Admin

- GET /admin/dashboard
- GET /admin/manajemenData-siswa
- GET /admin/manajemenData-siswa-import
- GET /admin/manajemenData-guru
- GET /admin/manajemen-kelas
- GET /admin/manajemen-mapel
- GET /admin/manajemen-guru-mapel
- GET /admin/manajemen-absensi
- GET /admin/manajemenAbsensi/{sesi_id}
- GET /admin/laporanAbsensi
- GET /admin/laporan-absensi/{kelas_id}

## 4.3 Guru

- GET /guru/dashboard
- GET /guru/buka-sesi
- GET /guru/live-absen/{token}
- GET /guru/manajemen-absensi
- GET /guru/manajemen/absensi/{sesi_id}
- GET /guru/laporanAbsensi
- GET /guru/guru/laporanAbsensi/detail/{id}

Catatan:

- Route detail laporan guru menghasilkan path /guru/guru/laporanAbsensi/detail/{id} karena prefix guru + path yang juga diawali guru.

## 4.4 Siswa

- GET /siswa/dashboard
- GET /siswa/riwayat-kehadiran
- GET /siswa/scan-absen

## 5. Model Data dan Relasi

## 5.1 users

Kolom penting:

- id
- name
- role: siswa|admin|guru|superadmin
- status: aktif|nonaktif|lulus|pindah
- jenis_kelamin: L|P
- user_photo
- email
- password

Catatan teknis:

- Model User memiliki fillable username, tetapi migrasi users tidak memiliki kolom username.

## 5.2 gurus

- id
- user_id (FK users)
- nip (unique, nullable)

Relasi:

- Guru belongsTo User
- Guru hasMany GuruMapel

## 5.3 kelas

- id
- guru_id (wali kelas, nullable FK gurus)
- is_active
- tingkat
- jurusan
- nama_kelas (unique)

Relasi:

- Kelas belongsTo waliKelas (Guru)
- Kelas hasMany Siswa
- Kelas hasMany GuruMapel

## 5.4 siswas

- id
- user_id (FK users)
- kelas_id (FK kelas, nullable)
- nisn (unique)

Relasi:

- Siswa belongsTo User
- Siswa belongsTo Kelas
- Siswa hasMany Absensi

## 5.5 mapels

- id
- kode_mapel (unique)
- nama_mapel
- kategori: umum|kejuruan

Relasi:

- Mapel hasMany GuruMapel

## 5.6 guru_mapels

- id
- kelas_id (FK kelas)
- mapel_id (FK mapels)
- guru_id (FK gurus)
- is_active
- softDeletes

Relasi:

- GuruMapel belongsTo Kelas
- GuruMapel belongsTo Mapel
- GuruMapel belongsTo Guru
- GuruMapel hasMany SesiAbsensi

## 5.7 sesi_absensis

- id
- guru_mapel_id (FK guru_mapels)
- tanggal
- waktu_mulai
- waktu_selesai (nullable)
- token_qr (nullable)
- status: berjalan|selesai

Relasi:

- SesiAbsensi belongsTo GuruMapel
- SesiAbsensi hasMany Absensi

## 5.8 absensis

- id
- sesi_absensi_id (FK sesi_absensis)
- siswa_id (FK siswas)
- waktu_scan (nullable)
- status: hadir|izin|sakit|alpa|terlambat
- keterangan (nullable)

Relasi:

- Absensi belongsTo Siswa
- Absensi belongsTo SesiAbsensi

## 6. Modul Fitur Per Peran

## 6.1 Modul Admin

### 6.1.1 Manajemen Siswa

Komponen:

- app/Livewire/Admin/ManajemenPengguna/Siswa/Index.php
- app/Livewire/Admin/ManajemenPengguna/Siswa/Form.php
- app/Livewire/Admin/ManajemenPengguna/Siswa/MutasiForm.php

Fitur:

- Search nama/NISN
- Filter kelas/status user
- Pagination
- Export Excel data siswa
- Hapus siswa + user jika belum punya riwayat absensi

### 6.1.2 Import Siswa Excel

Komponen:

- app/Livewire/Admin/ManajemenPengguna/Siswa/Import.php

Import class:

- app/Imports/SiswaImport.php

Aturan import:

- Header wajib: nama, nisn, email, jenis_kelamin
- Cek duplikasi email dan nisn
- Password default siswa = NISN (di-hash)
- DB transaction: rollback jika ada baris invalid

### 6.1.3 Manajemen Guru

Komponen:

- app/Livewire/Admin/ManajemenPengguna/Guru/Index.php
- app/Livewire/Admin/ManajemenPengguna/Guru/Form.php

Fitur:

- Search guru
- Export Excel guru
- Hapus guru ditolak jika:
    - masih memiliki riwayat mengajar/sesi
    - masih menjadi wali kelas

### 6.1.4 Manajemen Kelas

Komponen:

- app/Livewire/Admin/Kelas/Index.php
- app/Livewire/Admin/Kelas/Form.php

Fitur:

- CRUD kelas
- Pengaturan wali kelas
- status aktif/nonaktif kelas

### 6.1.5 Manajemen Mapel

Komponen:

- app/Livewire/Admin/Mapel/Index.php
- app/Livewire/Admin/Mapel/Form.php

Fitur:

- CRUD mata pelajaran

### 6.1.6 Penugasan Guru-Mapel

Komponen:

- app/Livewire/Admin/GuruMapel/Index.php
- app/Livewire/Admin/GuruMapel/Form.php

Fitur:

- Assign guru + mapel + kelas
- Aktivasi/nonaktivasi assignment

### 6.1.7 Manajemen Absensi Global

Komponen:

- app/Livewire/Admin/ManajemenAbsensi/Index.php
- app/Livewire/Admin/ManajemenAbsensi/Detail.php

Fitur:

- Filter sesi by kelas/mapel/tanggal
- Statistik per sesi (hadir/alpa/izin+sakit)
- Hapus sesi dan data absensi terkait

### 6.1.8 Laporan Absensi Admin

Komponen:

- app/Livewire/Admin/LaporanAbsensi/Index.php
- app/Livewire/Admin/LaporanAbsensi/Detail.php

Fitur:

- Filter semester ganjil/genap per tahun
- Statistik global (kelas, mapel, sesi, rata hadir)
- Daftar mapel per kelas + rata kehadiran
- Identifikasi siswa kritis (alpa >= 3)
- Export rekap per mapel+kelas+guru

## 6.2 Modul Guru

### 6.2.1 Buka Sesi Absensi

Komponen:

- app/Livewire/Guru/BukaSesiAbsen.php

Flow:

- Pilih mapel
- Pilih satu atau lebih kelas (berbasis guru_mapel)
- Validasi kelas memiliki siswa
- Generate token QR acak (Str::random(10))
- Buat record sesi_absensis per kelas dengan token sama

### 6.2.2 Live Monitor Absensi

Komponen:

- app/Livewire/Guru/LiveMonitorAbsen.php
  View:
- resources/views/livewire/guru/live-monitor-absen.blade.php

Fitur utama:

- Menampilkan QR sebagai token string current_qr_token
- Polling daftar siswa realtime
- Refresh token QR periodik (UI 10 detik) via refreshQR()
- Ubah status siswa manual: menunggu/hadir/terlambat/sakit/izin/alpa
- Batalkan sesi: hard delete sesi + absensi
- Tutup sesi: ubah status selesai, null token, auto insert alpa utk siswa belum scan

### 6.2.3 Manajemen Absensi Guru

Komponen:

- app/Livewire/Guru/ManajemenAbsensi.php

Fitur:

- Filter kelas/mapel/tanggal + search
- Statistik count hadir/alpa/izin+sakit per sesi
- Hapus sesi (dengan lock 7 hari)

### 6.2.4 Detail Absensi Sesi

Komponen:

- app/Livewire/Guru/DetailAbsensi.php

Fitur:

- Daftar absensi per sesi
- Ubah status absen
- Lock perubahan setelah 7 hari

### 6.2.5 Laporan Absensi Guru

Komponen:

- app/Livewire/Guru/LaporanAbsensi/Index.php
- app/Livewire/Guru/LaporanAbsensi/Detail.php

Fitur:

- Filter semester dan tahun
- Statistik global guru
- Daftar kelas-mapel yang diampu
- Siswa kritis (alpa >= 3)
- Export rekap Excel

## 6.3 Modul Siswa

### 6.3.1 Scan Absen

Komponen:

- app/Livewire/Siswa/ScanAbsen.php
  View:
- resources/views/livewire/siswa/scan-absen.blade.php

Fitur:

- Scan kamera (html5-qrcode) atau input token manual
- Validasi token aktif dan status sesi berjalan
- Validasi token harus sesuai kelas siswa
- Tolak duplicate attendance
- Simpan absensi status hadir + waktu_scan

### 6.3.2 Riwayat Kehadiran

Komponen:

- app/Livewire/Siswa/RiwayatKehadiran.php

Fitur:

- Filter riwayat
- Statistik kehadiran
- Pagination

## 7. Alur Teknis QR Attendance

## 7.1 Pembentukan Token

- Saat guru mulai sesi: token_qr dibuat random 10 karakter.
- Untuk multi kelas pada mapel yang sama: setiap sesi menggunakan token yang sama.

## 7.2 Token Dalam QR

- QR mengandung string token mentah, bukan JSON, bukan URL.
- View guru generate QR langsung dari token current_qr_token.

## 7.3 Validasi Saat Scan

Urutan validasi di ScanAbsen::prosesAbsen(token):

1. Cari sesi_absensis by token_qr + status berjalan.
2. Jika tidak ada, gagal (token invalid atau sesi ditutup).
3. Ambil siswa dari auth user.
4. Pastikan ada sesi yang kelasnya sama dengan kelas siswa.
5. Pastikan siswa belum absen pada sesi itu.
6. Insert absensis (status hadir, waktu_scan now).

## 7.4 Refresh Token

- Guru dapat refresh token QR (LiveMonitorAbsen::refreshQR).
- Semua sesi_id aktif pada monitor guru diupdate ke token baru.
- Implikasi: scanner siswa harus membaca QR terbaru.

## 7.5 Penutupan Sesi

### Manual:

- Guru klik tutup sesi.
- Sistem set status selesai, waktu_selesai terisi, token_qr null.
- Siswa yang belum scan otomatis dimasukkan status alpa.

### Otomatis:

- SesiAbsensi::tutupSesiOtomatis() menutup sesi berjalan yang sudah lewat 3 jam dari created_at.
- Proses ini juga menandai alpa siswa yang belum scan.
- Method dipanggil dari mount komponen tertentu (bukan scheduler).

## 8. Import/Export Data

## 8.1 Export Siswa/Guru

- app/Exports/SiswaExport.php
- app/Exports/GuruExport.php

Format:

- Headings terstruktur + style header berwarna.
- Data diambil via query Eloquent relasi.

## 8.2 Template Import Siswa

- app/Exports/SiswaTemplateExport.php
- Kolom: nama, nisn, email, jenis_kelamin

## 8.3 Rekap Absensi

- app/Exports/Guru/RekapAbsensiExport.php
- app/Exports/Admin/RekapAbsensiExport.php
- View export: resources/views/exports/rekap-absensi.blade.php

## 9. Security dan Integrity Rules

## 9.1 Akses

- Wajib auth sebelum masuk area role.
- CheckRole memblok akses role yang tidak sesuai.

## 9.2 Data Integrity

- Delete guru/siswa dibatasi jika sudah punya keterkaitan riwayat.
- sesi_absensi dan absensi memanfaatkan FK + cascadeOnDelete.

## 9.3 Risk Teknis Saat Ini

- Token QR adalah plain random string tanpa signature/expiry embedded.
- Tidak ada rate limit khusus endpoint scan karena belum API.
- Auto close sesi dipanggil saat page/component mount, bukan via scheduler.

## 10. Integrasi Flutter: Kondisi Saat Ini dan Rekomendasi

## 10.1 Kondisi Saat Ini

- API JSON untuk autentikasi dan absensi siswa sudah tersedia.
- Flutter tidak ideal jika langsung mengonsumsi endpoint web Livewire.
- Diperlukan endpoint API khusus mobile.

## 10.2 Rekomendasi Arsitektur Integrasi

Buat layer API baru:

- Auth API (login/logout/me)
- Attendance API (scan, active session, history)
- Optional guru API (open session, close session, refresh token) jika guru juga pakai mobile.

Paket disarankan:

- Laravel Sanctum untuk token auth mobile.

## 10.3 Kontrak API Minimum untuk Flutter Siswa

### POST /api/v1/auth/login

Request:
{
"email": "siswa@sekolah.sch.id",
"password": "**\*\***"
}

Response 200:
{
"token": "...",
"user": {
"id": 10,
"name": "Nama Siswa",
"role": "siswa",
"siswa_id": 25,
"kelas_id": 4
}
}

### POST /api/v1/attendance/scan

Headers:

- Authorization: Bearer <token>

Request:
{
"qr_token": "AbC123xYz0"
}

Response 200:
{
"message": "Berhasil absen",
"data": {
"sesi_absensi_id": 100,
"status": "hadir",
"waktu_scan": "07:15:21",
"tanggal": "2026-05-10",
"mapel": "Matematika",
"kelas": "X TKJ 1",
"guru": "Ibu Sari"
}
}

Error yang harus dikembalikan jelas:

- 400: token invalid / sesi ditutup
- 403: token bukan untuk kelas siswa
- 409: siswa sudah absen di sesi ini
- 401: token auth tidak valid

### GET /api/v1/attendance/history

Response:

- daftar riwayat absensi siswa + pagination + statistik ringkas.

### GET /api/v1/attendance/active

Response:

- jika ada sesi berjalan untuk kelas siswa, kirim info token metadata.

## 10.4 Mapping Logic Web ke API

Logika API scan WAJIB meniru ScanAbsen::prosesAbsen agar konsisten:

- query sesi by token+berjalan
- validasi kelas
- validasi duplicate
- insert absensi hadir + waktu_scan

## 10.5 Edge Case yang Harus Ditangani Flutter

- Token di-refresh guru saat siswa membuka scanner
- Sesi selesai saat siswa scan
- Siswa scan berulang
- Jaringan putus saat submit
- Jam device tidak sinkron (gunakan waktu server)

## 11. Setup Lokal dan Operasional

## 11.1 Setup Lokal

1. composer install
2. cp .env.example .env
3. php artisan key:generate
4. atur DB di .env
5. php artisan migrate
6. npm install
7. npm run dev
8. php artisan serve

## 11.2 Command Penting

- php artisan migrate:fresh --seed
- php artisan test
- php artisan queue:listen
- npm run build

## 12. Checklist Implementasi API Flutter (Actionable)

1. [SELESAI] Tambah routes/api.php dan versioning /api/v1.
2. [SELESAI] Install dan konfigurasi Laravel Sanctum.
3. [SELESAI] Buat AuthController API untuk login/logout/me.
4. [SELESAI] Buat AttendanceController API untuk scan/history/active.
5. Pindahkan logic scan ke service class terpisah agar dipakai oleh web dan API.
6. Tambah feature test untuk semua scenario scan.
7. [SELESAI] Tambah throttling endpoint scan.
8. Dokumentasikan API (OpenAPI/Swagger atau Postman collection).

## 13. Temuan Teknis yang Perlu Diperhatikan

1. Ketidaksesuaian schema-model:

- Model User memakai fillable username, migrasi users tidak membuat kolom username.

2. Route detail laporan guru:

- Path menjadi /guru/guru/laporanAbsensi/detail/{id}, kemungkinan tidak sengaja dobel prefix.

3. Auto-close sesi:

- Berbasis trigger saat komponen diakses, bukan scheduler periodik. Jika tidak ada traffic, sesi lama bisa tertunda ditutup.

4. Token QR:

- Token plain text tanpa metadata expiry; validasi expiry mengandalkan status sesi di DB.

## 14. Ringkasan Integrasi Flutter

Untuk target aplikasi Flutter scanner QR:

- Sistem web saat ini sudah memiliki business rule absensi yang jelas.
- Yang dibutuhkan adalah adapter API (bukan mengubah logic inti).
- Endpoint paling penting untuk MVP mobile siswa:
    - login
    - scan
    - history
    - profile/me

Dengan mengikuti kontrak API pada bagian 10, aplikasi Flutter dapat sinkron penuh dengan QR yang digenerate guru dari web ini.
