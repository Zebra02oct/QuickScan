<?php

namespace App\Livewire\Admin\GuruMapel;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use Livewire\Attributes\Computed;
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
    #[Title('Manajemen Guru Mapel')]

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $filter_kelas = '';

    #[Url(history: true)]
    public $filter_mapel = '';

    public function updated($property)
    {
        if (in_array($property, ['search', 'filter_kelas', 'filter_mapel'])) {
            $this->resetPage();
        }
    }

     #[On('refresh-guru-mapel')]
    public function refreshTable()
    {
        $this->resetPage();
    }

    #[Computed]
    public function list_kelas()
    {
        return Kelas::orderBy('tingkat', 'desc')->orderBy('nama_kelas')->get();
    }

    #[Computed]
    public function list_mapel()
    {
        return Mapel::orderBy('nama_mapel')->get();
    }

    #[Computed]
    public function gurus()
    {
        return Guru::with([
            'user', 
            'guruMapels' => function ($q) {
                if ($this->filter_kelas) $q->where('kelas_id', $this->filter_kelas);
                if ($this->filter_mapel) $q->where('mapel_id', $this->filter_mapel);
                $q->with(['mapel', 'kelas']);
            }
        ])
        ->whereHas('guruMapels', function ($q) {
            if ($this->filter_kelas) $q->where('kelas_id', $this->filter_kelas);
            if ($this->filter_mapel) $q->where('mapel_id', $this->filter_mapel);
        }) 
   
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($qUser) {
                    $qUser->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('guruMapels', function ($qGm) {
                    $qGm->whereHas('mapel', function ($qMapel) {
                        $qMapel->where('nama_mapel', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('kelas', function ($qKelas) {
                        $qKelas->where('nama_kelas', 'like', '%' . $this->search . '%');
                    });
                });
            });
        })
        ->paginate(10);
    }

    #[On('hapus-guru-mapel')]
    public function hapusDataGuruMapel($id)
    {
        $guruMapel = \App\Models\GuruMapel::find($id);

        if (!$guruMapel) {
            return;
        }

      
        $sudahPernahNgajar = Absensi::where('guru_mapel_id', $guruMapel->id)->exists();

        if ($sudahPernahNgajar) {
            $guruMapel->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Izin Dicabut!',
                'text'  => 'Penugasan Mapel Berhasil Dihapus (Soft Delete).'
            ]);
        } else {
            $guruMapel->forceDelete();
            
            $this->dispatch('swal:success', [
                'title' => 'Dihapus Permanen!',
                'text'  => 'Penugasan Mapel berhasil dihapus.'
            ]);
        }

        $this->refreshTable();
    }

    public function render()
    {
        return view('livewire.admin.guru-mapel.index');
    }
}