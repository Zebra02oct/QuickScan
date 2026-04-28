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
        // Kasih nama file dinamis pakai tanggal hari ini
        $namaFile = 'Data_Guru_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(new GuruExport, $namaFile);
    }

    #[On('hapus-data-guru')]
    public function hapusDataGuru($id)
    {
        $guru = Guru::find($id);

        if (!$guru) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Data guru tidak ditemukan.']);
            return;
        }

        DB::beginTransaction();

        try {
            $sebagaiWaliKelas = \App\Models\Kelas::where('guru_id', $guru->id)->exists();
            
            if ($sebagaiWaliKelas) {
                // BLOKIR! Jangan hapus apapun
                $this->dispatch('swal:error', [
                    'title' => 'Ditolak!',
                    'text'  => "Guru ini masih terdaftar sebagai Wali Kelas. Silakan ganti Wali Kelas tersebut di Manajemen Kelas terlebih dahulu."
                ]);
                return; 
            }

            $guruMapel = \App\Models\GuruMapel::where('guru_id', $guru->id)->exists();

            $user = User::find($guru->user_id);

            if ($guruMapel) {
                $guru->delete(); 
                if ($user) $user->delete(); 
                
              
            } else {
             
                $guru->forceDelete();
                if ($user) $user->forceDelete();
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => "Data Guru Berhasil Dihapus",
            ]);
            $this->dispatch('refresh-guru');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Error Sistem!',
                'text'  => 'Gagal menghapus data: ' . $e->getMessage()
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
