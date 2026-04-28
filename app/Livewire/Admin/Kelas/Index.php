<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\Kelas;
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
#[Title('Manajemen Kelas')]

  public $search = '';

     public function updatingSearch()
    {
        $this->resetPage();
    }

     public function getIsFilteredProperty()
    {
        return strlen($this->search) > 0;
    }

     public function resetSemuaFilter()
    {
        $this->reset(['search']);
        $this->resetPage();
    }

   #[On('refresh-kelas')]
    public function refreshTable()
    {
        $this->resetPage();
    }
  
 public function render()
    {
        $kelases = Kelas::with(['waliKelas'])
            ->withCount('siswas') 
            ->when($this->search, function ($query) {
                $query->where('nama_kelas', 'like', '%' . $this->search . '%');
            })
            ->orderBy('tingkat', 'desc') 
            ->orderBy('jurusan', 'asc')  
            ->orderBy('nama_kelas', 'asc') 
            ->paginate(10);

        return view('livewire.admin.kelas.index', [
            'kelases' => $kelases,
        ]);
    }



    #[On('hapus-data-kelas')]
    public function hapusDataKelas($id)
    {
        $kelas = \App\Models\Kelas::find($id);

        if (!$kelas) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Data kelas tidak ditemukan.']);
            return;
        }

        DB::beginTransaction();

        try {
            $jumlahSiswa = \App\Models\Siswa::where('kelas_id', $kelas->id)->count();
            if ($jumlahSiswa > 0) {
                $this->dispatch('swal:error', [
                    'title' => 'Ditolak!',
                    'text'  => "Tidak dapat menghapus kelas ini karena masih ada {$jumlahSiswa} siswa di dalamnya. Pindahkan siswa terlebih dahulu."
                ]);
                return; 
            }

         
            $guruMapel = \App\Models\GuruMapel::where('kelas_id', $kelas->id)->exists();
            if ($guruMapel) {
                $this->dispatch('swal:error', [
                    'title' => 'Ditolak!',
                    'text'  => "Tidak dapat menghapus kelas ini karena masih terikat dengan Mata Pelajaran Guru. Hapus Mata Pelajaran terkait terlebih dahulu."
                ]);
                return;
            }

            $namaKelas = $kelas->nama_kelas;
            $kelas->forceDelete(); 

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => "Kelas {$namaKelas} berhasil dihapus."
            ]);
            
            $this->dispatch('refresh-kelas'); 

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Error Sistem!',
                'text'  => 'Gagal menghapus kelas: ' . $e->getMessage()
            ]);
        }
    }
}