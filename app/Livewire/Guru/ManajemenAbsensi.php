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
        $guruId = $this->guruId;

        // Ambil guru_mapel_ids untuk sesi reguler
        $guruMapelIds = GuruMapel::where('guru_id', $guruId)->pluck('id');

        $query = SesiAbsensi::with(['guruMapel.mapel', 'guruMapel.kelas', 'kelas'])
            ->where(function ($q) use ($guruId, $guruMapelIds) {
                // Sesi reguler (dengan guru_mapel_id)
                $q->whereIn('guru_mapel_id', $guruMapelIds)
                    ->where('is_kelas_only', false);

                // Sesi kelas saja (wali kelas)
                $q->orWhere(function ($q2) use ($guruId) {
                    $q2->where('is_kelas_only', true)
                        ->where('wali_kelas_id', $guruId);
                });
            })
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
            $query->where(function ($q) {
                $q->whereHas('guruMapel', function ($q2) {
                    $q2->where('kelas_id', $this->filter_kelas_id);
                })->orWhere(function ($q3) {
                    $q3->where('is_kelas_only', true)
                        ->where('kelas_id', $this->filter_kelas_id);
                });
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
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('guruMapel.kelas', function ($q2) use ($search) {
                    $q2->where('nama_kelas', 'like', '%' . $search . '%');
                })->orWhereHas('guruMapel.mapel', function ($q3) use ($search) {
                    $q3->where('nama_mapel', 'like', '%' . $search . '%');
                })->orWhere(function ($q4) use ($search) {
                    $q4->where('is_kelas_only', true)
                        ->whereHas('kelas', function ($q5) use ($search) {
                            $q5->where('nama_kelas', 'like', '%' . $search . '%');
                        });
                });
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
               Absensi::where('sesi_absensi_id', $sesi->id)->delete();
                $sesi->delete();
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