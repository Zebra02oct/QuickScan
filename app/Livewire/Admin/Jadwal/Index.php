<?php

namespace App\Livewire\Admin\Jadwal;

use App\Models\Guru; // Pastikan G nya besar ya bray kalau modelmu pakai kapital
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Mapel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    #[Title('Manajemen Jadwal')]

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $filter_ta = '';
    
    #[Url(history: true)]
    public $filter_kelas = '';
    
    #[Url(history: true)]
    public $filter_mapel = '';

  
    public function mount()
    {
        $ta_aktif = TahunAjaran::where('status', 'aktif')->first();
        if ($ta_aktif) {
            $this->filter_ta = $ta_aktif->id;
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'filter_ta', 'filter_kelas', 'filter_mapel'])) {
            $this->resetPage();
        }
    }

    #[On('refresh-jadwal')]
    public function render()
    {
        $applyFilters = function ($query) {
            if ($this->filter_ta) {
                $query->where('tahun_ajaran_id', $this->filter_ta);
            }
            if ($this->filter_kelas) {
                $query->where('kelas_id', $this->filter_kelas);
            }
            if ($this->filter_mapel) {
                $query->where('mapel_id', $this->filter_mapel);
            }
        };

        $gurus = Guru::with([
                'user', 
                'jadwals' => function ($q) use ($applyFilters) {
                    $applyFilters($q);
                    $q->with(['mapel', 'kelas', 'tahunAjaran']);
                }
            ])
            // Hanya tampilkan guru yang punya jadwal (dan jadwalnya sesuai filter)
            ->whereHas('jadwals', $applyFilters) 
            ->where(function ($query) {
                if ($this->search) {
                    $query->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('jadwals', function ($q) {
                        $q->whereHas('mapel', function ($q2) {
                            $q2->where('nama_mapel', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('kelas', function ($q2) {
                            $q2->where('nama_kelas', 'like', '%' . $this->search . '%');
                        });
                    });
                }
            })
            ->paginate(10);

        return view('livewire.admin.jadwal.index', [
            'gurus'      => $gurus,
            'list_ta'    => TahunAjaran::orderBy('nama_ta', 'desc')->get(),
            'list_kelas' => Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(),
            'list_mapel' => Mapel::orderBy('nama_mapel')->get(),
        ]);
    }

    #[On('hapus-jadwal')]
    public function hapusJadwal($id)
    {
        try {
            $jadwal = JadwalPelajaran::findOrFail($id);
            $jadwal->delete();

            $this->dispatch('swal:success', [
                'title' => 'Terhapus!',
                'text'  => 'Plotting mengajar berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'Terjadi kesalahan saat menghapus data.'
            ]);
        }
    }
}