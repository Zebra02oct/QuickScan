<?php

namespace App\Livewire\Admin\ManajemenAbsensi;

use App\Models\Absensi;
use App\Models\SesiAbsensi;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Detail extends Component
{

 #[Layout('layouts.app')]
#[Title('Detail Absensi')]
    
    public $sesi_id;
    public $sesi;

  
    public function mount($sesi_id)
    {
        $this->sesi_id = $sesi_id;
        $this->sesi = SesiAbsensi::with(['guruMapel.kelas', 'guruMapel.mapel', 'guruMapel.guru.user'])->findOrFail($sesi_id);
    }


    #[Computed]
    public function daftarAbsen()
    {
        return Absensi::with('siswa.user')
            ->where('sesi_absensi_id', $this->sesi_id)
            ->get();
    }

    #[Computed]
    public function statistik()
    {
        $data = $this->daftarAbsen;

        return [
            'hadir' => $data->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin'  => $data->where('status', 'izin')->count(),
            'sakit' => $data->where('status', 'sakit')->count(),
            'alpa'  => $data->where('status', 'alpa')->count(),
        ];
    }

    public function ubahStatus($idAbsensi, $statusBaru)
    {
   

        $absen = Absensi::find($idAbsensi);
        if ($absen) {
            $absen->update(['status' => $statusBaru]);
            
             $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Status Berhasil Diubah.'
            ]);
        }
    }
    public function render()
    {
        return view('livewire.admin.manajemen-absensi.detail');
    }
}
