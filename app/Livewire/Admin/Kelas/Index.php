<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
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

    $namaKelas = $kelas->nama_kelas;

    $punyaAbsensi = \App\Models\GuruMapel::where('kelas_id', $kelas->id)
                        ->whereHas('sesiAbsensis')
                        ->exists();

        if ($punyaAbsensi) {
        $this->dispatch('swal:error', [
            'title' => 'Aksi Ditolak!',
            'text'  => "Kelas {$namaKelas} tidak bisa dihapus karena memiliki riwayat absensi. Silakan Edit dan ubah Status Kelas menjadi 'Nonaktif'!"
        ]);
        return;
    }

    $jumlahSiswa = \App\Models\Siswa::where('kelas_id', $kelas->id)->count();

    if ($jumlahSiswa > 0) {
        $this->dispatch('swal:error', [
            'title' => 'Aksi Ditolak!',
            'text'  => "Terdapat {$jumlahSiswa} siswa di kelas {$namaKelas}. Silakan pindahkan data siswa ke kelas lain terlebih dahulu!"
        ]);
        return;
    }

    $punyaTugas = \App\Models\GuruMapel::where('kelas_id', $kelas->id)->exists();

    if ($punyaTugas) {
        $this->dispatch('swal:error', [
            'title' => 'Aksi Ditolak!',
            'text'  => "Masih ada penugasan guru di kelas {$namaKelas}. Silakan hapus/ubah jadwal di menu Penugasan terlebih dahulu!"
        ]);
        return;
    }

    DB::beginTransaction();

    try {
        $kelas->delete();
        DB::commit();
    
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text'  => "Data Kelas {$namaKelas} berhasil dihapus."
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