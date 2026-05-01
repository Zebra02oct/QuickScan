<?php

namespace App\Livewire\Siswa;

use App\Models\SesiAbsensi;
use App\Models\Absensi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ScanAbsen extends Component
{
    #[Layout('layouts.app')]
    #[Title('Scan QR Absensi')]

    public $token_manual = '';
    public $pesan_sukses = '';
    public $pesan_error = '';

    
    public function prosesAbsen($token_dari_qr)
    {
     
        $this->pesan_sukses = '';
        $this->pesan_error = '';

    
        $sesiAktif = SesiAbsensi::with('guruMapel')
            ->where('token_qr', $token_dari_qr)
            ->where('status', 'berjalan')
            ->get();

        if ($sesiAktif->isEmpty()) {
            $this->pesan_error = 'QR Code tidak valid atau sesi sudah ditutup oleh Guru!';
            return;
        }

  
        $siswa = auth()->user()->siswa; 

      
        $sesiYangCocok = $sesiAktif->first(function ($sesi) use ($siswa) {
            return $sesi->guruMapel->kelas_id == $siswa->kelas_id;
        });

        if (!$sesiYangCocok) {
            $this->pesan_error = 'Gagal! Sesi absen ini bukan untuk kelas Anda.';
            return;
        }

     
        $sudahAbsen = Absensi::where('sesi_absensi_id', $sesiYangCocok->id)
            ->where('siswa_id', $siswa->id)
            ->exists();

        if ($sudahAbsen) {
            $this->pesan_error = 'Kamu sudah berhasil melakukan absensi untuk pertemuan ini.';
            return;
        }

      
        Absensi::create([
            'sesi_absensi_id' => $sesiYangCocok->id,
            'siswa_id'        => $siswa->id,
            'waktu_scan'      => now()->toTimeString(),
            'status'          => 'hadir',
        ]);

        $this->pesan_sukses = 'Berhasil! Kehadiranmu telah tercatat.';
        
  
    }

    public function render()
    {
        return view('livewire.siswa.scan-absen');
    }
}