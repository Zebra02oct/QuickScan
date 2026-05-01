<?php

namespace App\Livewire\Guru;

use App\Models\Absensi;
use App\Models\GuruMapel;
use App\Models\SesiAbsensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenAbsensi extends Component
{
    use WithPagination;

      #[Layout('layouts.app')]
    #[Title('Manajeman Absensi')]
    
    public $search = '';
    public $filter_kelas_id = '';
    public $filter_mapel_id = '';

    public function updating($property)
    {
        if (in_array($property, ['search', 'filter_kelas_id', 'filter_mapel_id'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function guruId()
    {
        return Auth::user()->guru->id; 
    }
    
      #[Computed]
    public function myGuruMapelIds()
    {
        return GuruMapel::where('guru_id', $this->guruId)->pluck('id');
    }

      #[Computed]
    public function daftarKelas()
    {
        return GuruMapel::where('guru_id', $this->guruId)
            ->with('kelas')
            ->get()
            ->pluck('kelas')
            ->unique('id')
            ->values();
    }

    #[Computed]
    public function daftarMapel()
    {
        return GuruMapel::where('guru_id', $this->guruId)
            ->with('mapel')
            ->get()
            ->pluck('mapel')
            ->unique('id')
            ->values();
    }


    #[Computed]
    public function daftarSesi()
    {
        $query = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas'])
            ->whereIn('guru_mapel_id', $this->myGuruMapelIds)
            ->withCount([
                'absensis as hadir_count' => function ($q) {
                    $q->whereIn('status', ['hadir', 'terlambat']);
                },
                'absensis as alpa_count' => function ($q) {
                    $q->where('status', 'alpa');
                },
                'absensis as izin_sakit_count' => function ($q) {
                    $q->whereIn('status', ['izin', 'sakit']);
                }
            ]);

        if ($this->filter_kelas_id) {
            $query->whereHas('guruMapel', function ($q) {
                $q->where('kelas_id', $this->filter_kelas_id);
            });
        }

        if ($this->filter_mapel_id) {
            $query->whereHas('guruMapel', function ($q) {
                $q->where('mapel_id', $this->filter_mapel_id);
            });
        }

        if ($this->search) {
            $query->whereHas('guruMapel.kelas', function ($q) {
                $q->where('nama_kelas', 'like', '%' . $this->search . '%');
            })->orWhereHas('guruMapel.mapel', function ($q) {
                $q->where('nama_mapel', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest('tanggal')->paginate(10);
    }


#[On('hapus-data-absen')]
public function hapusSesi($id)
{
    $sesi = SesiAbsensi::find($id);

    if ($sesi) {
        $isLocked = \Carbon\Carbon::parse($sesi->created_at)->addDays(7)->isPast();
        
        if ($isLocked) {
            // Sesi terkunci
            $this->dispatch('swal:error', [
                'title' => 'Gagal Dihapus',
                'text'  => 'Sesi sudah terkunci karena melewati batas 7 hari.'
            ]);
            return; 
        }

        try {
            DB::transaction(function () use ($sesi) {
               Absensi::where('sesi_absensi_id', $sesi->id)->forceDelete();
                $sesi->forceDelete();
            });

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => 'Data sesi beserta riwayat absen berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Terjadi Kesalahan Sistem',
                'text'  => 'Gagal menghapus data. Pesan: ' . $e->getMessage()
            ]);
        }
    }
}

    public function render()
    {
        return view('livewire.guru.manajemen-absensi');
    }
}