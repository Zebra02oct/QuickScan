<?php

namespace App\Livewire\Admin\ManajemenAbsensi;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\SesiAbsensi;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    #[Title('Manajemen Absensi')]
    
    public $search = '';
    public $filter_kelas_id = '';
    public $filter_mapel_id = '';
    public $tanggal_mulai = '';
    public $tanggal_akhir = '';

    public function updating($property)
    {
        if (in_array($property, ['search', 'filter_kelas_id', 'filter_mapel_id', 'tanggal_mulai', 'tanggal_akhir'])) {
            $this->resetPage();
        }
    }

    public function resetFilter()
    {
        $this->reset(['search', 'filter_kelas_id', 'filter_mapel_id', 'tanggal_mulai', 'tanggal_akhir']);
        $this->resetPage();
    }

    #[Computed]
    public function daftarKelas()
    {
        return Kelas::orderBy('nama_kelas', 'asc')->get();
    }

    #[Computed]
    public function daftarMapel()
    {
        return Mapel::orderBy('nama_mapel', 'asc')->get();
    }

    #[Computed]
    public function daftarSesi()
    {
        $query = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas', 'guruMapel.guru.user'])
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
        

        if ($this->tanggal_mulai && $this->tanggal_akhir) {
            $query->whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_akhir]);
        } elseif ($this->tanggal_mulai) {
            $query->where('tanggal', '>=', $this->tanggal_mulai);
        } elseif ($this->tanggal_akhir) {
            $query->where('tanggal', '<=', $this->tanggal_akhir);
        }

        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->whereHas('guruMapel.kelas', function ($q) {
                    $q->where('nama_kelas', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('guruMapel.mapel', function ($q) {
                    $q->where('nama_mapel', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('guruMapel.guru.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        return $query->latest('tanggal')->paginate(10);
    }

    #[On('hapus-data-absen')]
    public function hapusSesi($id)
    {
        $sesi = SesiAbsensi::find($id);

        try {
            DB::transaction(function () use ($sesi) {
             
                Absensi::where('sesi_absensi_id', $sesi->id)->delete();
                $sesi->delete();
            });

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => 'Data sesi beserta riwayat absen berhasil dihapus permanen.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Terjadi Kesalahan Sistem',
                'text'  => 'Gagal menghapus data. Pesan: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.manajemen-absensi.index');
    }
}