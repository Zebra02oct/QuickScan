<?php

namespace App\Livewire\Siswa;

use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination; 

class RiwayatKehadiran extends Component
{
    use WithPagination;

     #[Layout('layouts.app')]
    #[Title('Riwayat Kehadiran')]

    public $filter_kelas_id = '';
    public $filter_mapel_id = '';
    public $search_guru = '';

    public function updating($property)
    {
        if (in_array($property, ['filter_kelas_id', 'filter_mapel_id', 'search_guru'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function getSiswaIdProperty()
    {
        return Auth::user()->siswa->id; 
    }

    #[Computed]
    public function daftarKelas()
    {
        return Absensi::where('siswa_id', $this->siswaId)
            ->with('sesiAbsensi.guruMapel.kelas')
            ->get()
            ->pluck('sesiAbsensi.guruMapel.kelas')
            ->unique('id')
            ->values();
    }

    #[Computed]
    public function daftarMapel()
    {
        return Absensi::where('siswa_id', $this->siswaId)
            ->with('sesiAbsensi.guruMapel.mapel')
            ->get()
            ->pluck('sesiAbsensi.guruMapel.mapel')
            ->unique('id')
            ->values();
    }

private function baseQuery()
    {
        $query = Absensi::with([
            'sesiAbsensi.guruMapel.mapel', 
            'sesiAbsensi.guruMapel.kelas', 
            'sesiAbsensi.guruMapel.guru.user'
        ])->where('siswa_id', $this->siswaId);

        if ($this->filter_kelas_id) {
            $query->whereHas('sesiAbsensi.guruMapel', function ($q) {
                $q->where('kelas_id', $this->filter_kelas_id);
            });
        }

        if ($this->filter_mapel_id) {
            $query->whereHas('sesiAbsensi.guruMapel', function ($q) {
                $q->where('mapel_id', $this->filter_mapel_id);
            });
        }

        if ($this->search_guru) {
            $query->whereHas('sesiAbsensi.guruMapel.guru.user', function ($q) {
                $q->where('name', 'like', '%' . $this->search_guru . '%');
            });
        }

        return $query;
    }

#[Computed]
    public function statistik()
    {
        $data = $this->baseQuery()->get();

        return [
            'hadir' => $data->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin'  => $data->where('status', 'izin')->count(),
            'sakit' => $data->where('status', 'sakit')->count(),
            'alpa'  => $data->where('status', 'alpa')->count(),
        ];
    }

    #[Computed]
    public function riwayatAbsen()
    {
        return $this->baseQuery()->latest('created_at')->paginate(10);
    }

    public function render()
    {
        return view('livewire.siswa.riwayat-kehadiran');
    }
}