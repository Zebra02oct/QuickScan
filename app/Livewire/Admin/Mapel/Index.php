<?php

namespace App\Livewire\Admin\Mapel;

use App\Models\Mapel;
use Illuminate\Support\Facades\DB;
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
#[Title('Manajemen Mapel')]

    #[Url(history: true)]
    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('refresh-mapel')]
    public function render()
    {
        $mapels = Mapel::query()
            ->where(function($query) {
                $query->where('nama_mapel', 'like', '%' . $this->search . '%')
                      ->orWhere('kode_mapel', 'like', '%' . $this->search . '%');
            })
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_mapel', 'asc')
            ->paginate(10);

        return view('livewire.admin.mapel.index', [
            'mapels' => $mapels
        ]);
    }

#[On('hapus-mapel')]
public function hapusDataMapel($id)
{
    $mapel = \App\Models\Mapel::find($id);

    if (!$mapel) {
        $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Data Mapel tidak ditemukan.']);
        return;
    }
    $namaMapel = $mapel->nama_mapel; 

   
    $punyaPenugasan = \App\Models\GuruMapel::where('mapel_id', $mapel->id)->exists();
    
    if ($punyaPenugasan) {
        $this->dispatch('swal:error', [
            'title' => 'Aksi Ditolak!',
            'text'  => "Mata pelajaran {$namaMapel} masih terhubung dengan jadwal Penugasan Guru. Penghapusan tidak dapat dilakukan sebelum penugasan tersebut dihapus!"
        ]);
        return;
    }

    DB::beginTransaction();

    try {
        $mapel->delete();

        DB::commit();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text'  => "Mata pelajaran {$namaMapel} berhasil dihapus."
        ]);
        
        $this->dispatch('refresh-mapel');

    } catch (\Exception $e) {
        DB::rollBack(); 
        $this->dispatch('swal:error', [
            'title' => 'Error Sistem!',
            'text'  => 'Gagal menghapus mapel: ' . $e->getMessage()
        ]);
    }
}
}