<?php

namespace App\Livewire\Admin\ManajemenPengguna\Siswa;

use App\Exports\SiswaExport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Manajemen Siswa')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterKelas = '';
    public $filterStatus = '';

    public function updated($property)
    {
        if (in_array($property, ['search', 'filterKelas', 'filterStatus'])) {
            $this->resetPage();
        }
    }

   
     #[On('refresh-siswa')]
    public function refreshTable()
    {
        $this->resetPage();
    }

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

    public function exportExcel()
    {
      
        $namaFile = 'Data_Siswa_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(new SiswaExport, $namaFile);
    }

  #[On('hapus-data-siswa')]
    public function hapusDataSiswa($id)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'Data siswa tidak ditemukan di sistem.'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $punyaAbsensi = Siswa::where('id', $id)->whereHas('absensis')->exists();
            $user = User::find($siswa->user_id);

            if ($punyaAbsensi) {
            
                $siswa->delete(); 
        
                if ($user) {
                    $user->delete(); 
                }

               
            } else {
              
                $siswa->forceDelete();
                
                if ($user) {
                    $user->forceDelete(); 
                }
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil Dihapus!',
                'text'  => 'Data Mahasiswa Berhasil Dihapus'
            ]);

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
        $dataSiswa = Siswa::with(['user', 'kelas'])
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterKelas, function ($query) {
                $query->where('kelas_id', $this->filterKelas);
            })
            ->when($this->filterStatus, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('status', $this->filterStatus);
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.manajemen-pengguna.siswa.index', [
            'siswa' => $dataSiswa,
            'list_kelas' => Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get() 
        ]);
    }
}