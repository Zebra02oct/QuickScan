<?php

use App\Livewire\Admin\GuruMapel\index as GuruMapel;
use App\Livewire\Admin\Kelas\Index as ManajemenKelas;
use App\Livewire\Admin\ManajemenAbsensi\Detail;
use App\Livewire\Admin\ManajemenAbsensi\Index as ManajemenAbsensiForAdmin;
use App\Livewire\Admin\ManajemenPengguna\Guru\Index as ManajemenGuru;
use App\Livewire\Admin\ManajemenPengguna\Siswa\Import as ImportDataSiswa;
use App\Livewire\Admin\ManajemenPengguna\Siswa\Index as ManajemenSiswa;
use App\Livewire\Admin\Mapel\Index as ManajuemenMapel;
use App\Livewire\Dashboard\Admin\Index as AdminDashboard;
use App\Livewire\Dashboard\Guru\Index as GuruDashboard;
use App\Livewire\Dashboard\Siswa\Index as SiswaDashboard;
use App\Livewire\Guru\BukaSesiAbsen;
use App\Livewire\Guru\DetailAbsensi;
use App\Livewire\Guru\LaporanAbsensi\Index as LaporanAbsensiGuru;
use App\Livewire\Guru\LiveMonitorAbsen;
use App\Livewire\Guru\ManajemenAbsensi;
use App\Livewire\Siswa\RiwayatKehadiran;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->get('/dashboard', function () {
    $role = request()->user()?->role;

    $urlTujuan = match ($role) {
        'admin' => route('admin.dashboard'),
        'guru'  => route('guru.dashboard'),
        'siswa' => route('siswa.dashboard'),
        default => '/', 
    };

    return redirect()->intended($urlTujuan);
})->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');

    Route::get('/manajemenData-siswa', ManajemenSiswa::class)->name('admin.siswa.index');
    Route::get('/manajemenData-siswa-import', ImportDataSiswa::class)->name('admin.siswa.import');
    Route::get('/manajemenData-guru', ManajemenGuru::class)->name('admin.guru.index');
    Route::get('/manajemen-kelas', ManajemenKelas::class)->name('admin.kelas.index');
    Route::get('/manajemen-mapel', ManajuemenMapel::class)->name('admin.mapel.index');
    Route::get('/manajemen-guru-mapel', GuruMapel::class)->name('admin.guruMapel.index');
      Route::get('/manajemen-absensi', ManajemenAbsensiForAdmin::class)->name('admin.manajemenAbsensi.index');
            Route::get('/manajemenAbsensi/{sesi_id}', Detail::class)->name('admin.manajemenAbsensi.detail');
});

Route::middleware(['auth' ,'role:guru'])->prefix('guru')->group(function () {
    Route::get('/dashboard', GuruDashboard::class)->name('guru.dashboard');
    Route::get('/buka-sesi',BukaSesiAbsen::class)->name('guru.absen.buka');
Route::get('/live-absen/{token}', LiveMonitorAbsen::class)->name('guru.absen.live');
  Route::get('/manajemen-absensi', ManajemenAbsensi::class)->name('guru.manajemenAbsensi');
  Route::get('/manajemen/absensi/{sesi_id}', DetailAbsensi::class)->name('guru.detailAbsensi');

    Route::get('/laporan-absensi', LaporanAbsensiGuru::class)->name('guru.laporanAbsensi');
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->group(function () {
     Route::get('/dashboard', SiswaDashboard::class)->name('siswa.dashboard');
     Route::get('/riwayat-kehadiran', RiwayatKehadiran::class)->name('siswa.riwayatKehadiran');
    Route::get('/scan-absen', \App\Livewire\Siswa\ScanAbsen::class)->name('siswa.absen.scan');

});

require __DIR__.'/auth.php';