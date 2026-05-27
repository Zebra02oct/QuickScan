<?php

namespace App\Livewire\Guru;

use App\Models\Absensi;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class DetailAbsensi extends Component
{

    #[Layout('layouts.app')]
    #[Title('Detail Absensi')]

    public $sesi_id;
    public $sesi;


    public function mount($sesi_id)
    {
        $this->sesi_id = $sesi_id;

        $this->sesi = SesiAbsensi::with([
            'guruMapel.mapel',
            'guruMapel.kelas',
        ])->findOrFail($sesi_id);
    }

    #[Computed]
    public function isLocked()
    {
        return Carbon::parse($this->sesi->created_at)->addDays(7)->isPast();
    }

    #[Computed]
    public function daftarAbsen()
    {
        return Absensi::with(['siswa.user', 'sesiAbsensi.guruMapel.mapel', 'sesiAbsensi.guruMapel.kelas'])
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
        if ($this->isLocked) {
            $this->dispatch('swal:error', [
                'title' => 'Terkunci!',
                'text' => 'Data sesi ini sudah melewati batas 7 hari dan tidak bisa diubah.'
            ]);
            return;
        }

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
        return view('livewire.guru.detail-absensi');
    }
}
