<?php

namespace App\Livewire\Admin\ManajemenPengguna\Siswa;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MutasiForm extends Component
{
    public $kelas_asal_id = '';
    public $kelas_tujuan_id = '';
    
    public $siswa_ids = []; 
    public $list_siswa = []; 
    
    
    public $list_kelas_tujuan = [];
    public $is_lulus = false;

    public function loadData(){
           $this->reset();
        $this->resetValidation();
    }

    #[Computed]
    public function listKelas()
    {
        return Kelas::select('id', 'nama_kelas', 'is_active')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
    }

    public function updatedKelasAsalId($value)
    {
        $this->list_kelas_tujuan = [];
        $this->is_lulus = false;
        $this->kelas_tujuan_id = '';

        if ($value) {
            $dataSiswa = Siswa::with('user')
                ->where('kelas_id', $value)
                ->whereHas('user', function($q) {
                    $q->where('status', 'aktif'); 
                })
                ->get();

            $this->list_siswa = $dataSiswa;
            $this->siswa_ids = $dataSiswa->pluck('id')->map(fn($id) => (string) $id)->toArray();

          
            $kelasAsal = Kelas::find($value);
            
            if ($kelasAsal) {
            
                $tingkat = strtoupper($kelasAsal->tingkat); 

             if ($tingkat === 'X' ) {
                    $this->list_kelas_tujuan = Kelas::whereIn('tingkat', ['XI'])
                                                    ->where('is_active', 1)
                                                    ->get();
                } 
                elseif ($tingkat === 'XI') {
                    $this->list_kelas_tujuan = Kelas::whereIn('tingkat', ['XII'])
                                                    ->where('is_active', 1)
                                                    ->get();
                } 
                elseif ($tingkat === 'XII' ) {
                    $this->is_lulus = true;
                    $this->kelas_tujuan_id = 'lulus';
                }
            }

        } else {
            $this->list_siswa = [];
            $this->siswa_ids = [];
        }
    }

    public function save()
    {
        $this->validate([
            'kelas_asal_id'   => 'required|exists:kelas,id',
            'kelas_tujuan_id' => 'required',
            'siswa_ids'       => 'required|array|min:1',
        ], [
            'kelas_asal_id.required'   => 'Pilih kelas asal terlebih dahulu.',
            'kelas_tujuan_id.required' => 'Pilih tujuan kenaikan kelas atau kelulusan.',
            'siswa_ids.required'       => 'Pilih minimal satu siswa untuk diproses.',
        ]);

        DB::beginTransaction();

        try {
            if ($this->is_lulus && $this->kelas_tujuan_id === 'lulus') {
           
                $userIds = Siswa::whereIn('id', $this->siswa_ids)->pluck('user_id')->toArray();
                
            
                User::whereIn('id', $userIds)->update(['status' => 'lulus']);
                
                Siswa::whereIn('id', $this->siswa_ids)->update(['kelas_id' => null]);
                
                $pesan = count($this->siswa_ids) . ' siswa Kelas XII berhasil diluluskan!';
                
            } else {
           
                
                Siswa::whereIn('id', $this->siswa_ids)->update([
                    'kelas_id' => $this->kelas_tujuan_id
                ]);
                
                $pesan = count($this->siswa_ids) . ' siswa berhasil dinaikkan kelasnya!';
            }

            DB::commit();

            $this->dispatch('close-modal');
            $this->dispatch('refresh-siswa'); 
            $this->dispatch('swal:success', [
                'title' => 'Mantap!',
                'text'  => $pesan
            ]);

            $this->reset();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'Terjadi kesalahan saat memproses data: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.manajemen-pengguna.siswa.mutasi-form');
    }
}