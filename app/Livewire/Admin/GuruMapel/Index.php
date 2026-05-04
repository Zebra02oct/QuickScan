<?php

namespace App\Livewire\Admin\GuruMapel;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\SesiAbsensi;
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

       #[Url(history: true)]
   public $filter_status = '1';

    public function updated($property)
    {
        if (in_array($property, ['search', 'filter_kelas', 'filter_mapel','filter_status'])) {
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
                if ($this->filter_status !== '') $q->where('is_active', $this->filter_status);
                $q->with(['mapel', 'kelas']);
            }
        ])
        ->whereHas('guruMapels', function ($q) {
            if ($this->filter_kelas) $q->where('kelas_id', $this->filter_kelas);
            if ($this->filter_mapel) $q->where('mapel_id', $this->filter_mapel);
            if ($this->filter_status !== '') $q->where('is_active', $this->filter_status); 
        }) 
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($qUser) {
                    $qUser->where('name', 'like', '%' . $this->search . '%');
                })

                ->orWhereHas('guruMapels', function ($qGm) {
                    if ($this->filter_kelas) $qGm->where('kelas_id', $this->filter_kelas);
                    if ($this->filter_mapel) $qGm->where('mapel_id', $this->filter_mapel);
                    if ($this->filter_status !== '') $qGm->where('is_active', $this->filter_status);

                    $qGm->where(function ($qSub) {
                        $qSub->whereHas('mapel', function ($qMapel) {
                            $qMapel->where('nama_mapel', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('kelas', function ($qKelas) {
                            $qKelas->where('nama_kelas', 'like', '%' . $this->search . '%');
                        });
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
        $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Data Penugasan Guru tidak ditemukan.']);
        return;
    }

    $sudahPernahNgajar = \App\Models\SesiAbsensi::where('guru_mapel_id', $guruMapel->id)->exists();

   if ($sudahPernahNgajar) {
    $this->dispatch('swal:error', [
        'title' => 'Aksi Ditolak!',
        'text'  => 'Penugasan mata pelajaran ini sudah memiliki riwayat absensi. Data tidak dapat dihapus karena akan memengaruhi laporan kehadiran. Jika sudah tidak digunakan, silakan ubah statusnya menjadi Nonaktif.'
    ]);
    return;
}

    \Illuminate\Support\Facades\DB::beginTransaction();

    try {

        $guruMapel->delete();
        
        \Illuminate\Support\Facades\DB::commit();
        
        $this->dispatch('swal:success', [
            'title' => 'Berhasil Dihapus!',
            'text'  => 'Data Penugasan Mapel berhasil dihapus.'
        ]);
        
        $this->refreshTable(); 
     
    } catch (\Exception $e) {
      
        \Illuminate\Support\Facades\DB::rollBack();
        
        $this->dispatch('swal:error', [
            'title' => 'Error Sistem!',
            'text'  => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
        ]);
    }
}
    public function render()
    {
        return view('livewire.admin.guru-mapel.index');
    }
}