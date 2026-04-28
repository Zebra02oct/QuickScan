<?php

use App\Livewire\Admin\Jadwal\Index as ManajemenJadwal;
use App\Livewire\Admin\Kelas\Index as ManajemenKelas;
use App\Livewire\Admin\ManajemenPengguna\Guru\Index as ManajemenGuru;
use App\Livewire\Admin\ManajemenPengguna\Siswa\Import as ImportDataSiswa;
use App\Livewire\Admin\ManajemenPengguna\Siswa\Index as ManajemenSiswa;
use App\Livewire\Admin\Mapel\Index as ManajuemenMapel;
use App\Livewire\Dashboard\Admin\Index as AdminDashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    Route::get('/manajemenData-siswa', ManajemenSiswa::class)->name('admin.siswa.index');
    Route::get('/manajemenData-siswa-import', ImportDataSiswa::class)->name('admin.siswa.import');
    Route::get('/manajemenData-guru', ManajemenGuru::class)->name('admin.guru.index');
    Route::get('/manajemen-kelas', ManajemenKelas::class)->name('admin.kelas.index');
    Route::get('/manajemen-mapel', ManajuemenMapel::class)->name('admin.mapel.index');
    Route::get('/manajemen-jadwal', ManajemenJadwal::class)->name('admin.jadwal.index');
});

require __DIR__.'/auth.php';