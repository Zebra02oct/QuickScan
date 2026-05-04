<?php

namespace App\Livewire\Admin\ManajemenPengguna\Guru;

use App\Exports\GuruExport;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;
    

#[Layout('layouts.app')]
#[Title('Manajemen Guru')]

  
    public $search = '';

     public function updatingSearch()
    {
        $this->resetPage();
    }

      #[On('refresh-guru')]
    public function refreshTable()
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

      public function exportExcel()
    {
       
        $namaFile = 'Data_Guru_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(new GuruExport, $namaFile);
    }

    #[On('hapus-data-guru')]
    public function hapusDataGuru($id)
    {
        $guru = Guru::with('user')->find($id);

        if (!$guru) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Data guru tidak ditemukan.']);
            return;
        }

        DB::beginTransaction();

        try {
            $namaGuru = $guru->user->nama;
           $punyaAbsensi = \App\Models\GuruMapel::where('guru_id', $guru->id)
                            ->whereHas('sesiAbsensis')
                            ->exists();

            if ($punyaAbsensi) {
            DB::rollBack();
            $this->dispatch('swal:error', [
     'title' => 'Aksi Ditolak!',
'text'  => "Guru {$namaGuru} memiliki riwayat mengajar atau absensi. Penghapusan tidak dapat dilakukan. Silakan ubah status menjadi Nonaktif atau hapus riwayat absensi terlebih dahulu."
            ]);
            return;
        }

$sebagaiWaliKelas = \App\Models\Kelas::where('guru_id', $guru->id)->exists();
            
            if ($sebagaiWaliKelas) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Aksi Ditolak!',
                'text'  => "Guru {$namaGuru} masih menjabat sebagai Wali Kelas. Silakan ganti Wali Kelas di menu Manajemen Kelas terlebih dahulu."
            ]);
            return;
        }

        \App\Models\GuruMapel::where('guru_id', $guru->id)->delete();

        $user = \App\Models\User::find($guru->user_id);
        
     
        $guru->delete();

    
        if ($user) {
            $user->delete();
        }

        DB::commit();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil Dihapus!',
            'text'  => "Data Guru {$namaGuru} berhasil dihapus."
        ]);

        $this->dispatch('refresh-guru');

    } catch (\Exception $e) {
        DB::rollBack();
        $this->dispatch('swal:error', [
            'title' => 'Error Sistem!',
            'text'  => 'Gagal menghapus data guru: ' . $e->getMessage()
        ]);
    }
}

    public function render()
    {
              
     $guru = Guru::with('user')
     ->whereHas('user', function($q) {
            $q->where('name', 'like', '%'.$this->search. '%')
            ->orWhere('nip', 'like', '%'.$this->search.'%');
        })->paginate(10);

        return view('livewire.admin.manajemen-pengguna.guru.index', [
            'guru' => $guru
        ]);
    }
}
