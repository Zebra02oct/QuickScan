<?php

namespace App\Livewire\Admin\GuruMapel;

use App\Models\Guru;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\SesiAbsensi;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Form extends Component
{
    public $guru_mapel_id = null;
    public $guru_id = '';
    public $mapel_id = '';
    public $kelas_id = '';

    public $is_active = 1;
    public $has_history = false;

    protected function rules()
    {
        return [
            'guru_id'  => 'required|exists:gurus,id',
            'mapel_id' => 'required|exists:mapels,id',
            'kelas_id' => 'required|exists:kelas,id',
            'is_active' => 'required|boolean',
        ];
    }

    protected $messages = [
        'guru_id.required'  => 'Guru wajib dipilih.',
        'mapel_id.required' => 'Mata Pelajaran wajib dipilih.',
        'kelas_id.required' => 'Kelas wajib dipilih.',
        'is_active.required' => 'Status Penugasan wajib dipilih.',
    ];

  #[Computed]
    public function listGuru()
    {
        return Guru::with('user')
            ->where(function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('status', 'aktif'); 
                });
                if ($this->guru_id) {
                    $query->orWhere('id', $this->guru_id);
                }
            })
            ->get()
            ->sortBy(fn($guru) => $guru->user->name ?? 'Z'); 
    }

    #[Computed]
    public function listMapel()
    {
        return Mapel::orderBy('nama_mapel')->get();
    }

  #[Computed]
    public function listKelas()
    {
        return Kelas::select('id', 'nama_kelas', 'is_active', 'tingkat')
            ->where(function ($query) {
                $query->where('is_active', 1);
                if ($this->kelas_id) {
                    $query->orWhere('id', $this->kelas_id);
                }
            })
            ->orderBy('tingkat', 'desc')
            ->orderBy('nama_kelas')
            ->get();
    }

    public function loadData($id = null)
    {
        $this->reset();
        $this->resetValidation();

        $this->is_active = 1;
        $this->has_history = false;

        if ($id) {
            $data = GuruMapel::find($id);
            if ($data) {
                $this->guru_mapel_id = $data->id;
                $this->guru_id       = $data->guru_id;
                $this->mapel_id      = $data->mapel_id;
                $this->kelas_id      = $data->kelas_id;
                $this->is_active     = $data->is_active;

                $this->has_history = SesiAbsensi::where('guru_mapel_id', $id)->exists();
            }
        }
    }

    public function save()
    {
    if ($this->guru_mapel_id) {
            $jadwalLama = GuruMapel::find($this->guru_mapel_id);
            $this->guru_id = $jadwalLama->guru_id; 

            $adaPerubahan = ($jadwalLama->mapel_id != $this->mapel_id) || 
                                   ($jadwalLama->kelas_id != $this->kelas_id);

            if ($this->has_history && $adaPerubahan) {
                $this->dispatch('close-modal');
                $this->dispatch('swal:error', [
                    'title' => 'Edit Ditolak!',
                    'text'  => 'Jadwal ini sudah memiliki riwayat transaksi/absensi. Mengubah Mapel atau Kelas akan merusak data absensi! Silakan Nonaktifkan jadwal ini dan buat yang baru.'
                ]);
                return;
            }
        }
        $this->validate();

        
        $isDuplicate = GuruMapel::where('guru_id', $this->guru_id)
            ->where('mapel_id', $this->mapel_id)
            ->where('kelas_id', $this->kelas_id)
            ->when($this->guru_mapel_id, function ($query) {
                $query->where('id', '!=', $this->guru_mapel_id);
            })
            ->exists();

       

        if ($isDuplicate) {
              $this->dispatch('close-modal');
            $this->dispatch('swal:error', [
                'title' => 'Gagal Disimpan!',
                'text'  => 'Penugasan Mapel sudah ada. Guru ini sudah mengajar mapel tersebut di kelas yang dipilih.'
            ]);
            return;
        }

     

        GuruMapel::updateOrCreate(
            ['id' => $this->guru_mapel_id],
            [
                'guru_id'  => $this->guru_id,
                'mapel_id' => $this->mapel_id,
                'kelas_id' => $this->kelas_id,
                'is_active' => $this->is_active,
            ]
        );

        $this->dispatch('close-modal');
        $this->dispatch('refresh-guru-mapel');
        
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text'  => 'Data Penugasan Mapel Guru berhasil disimpan.'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.guru-mapel.form');
    }
}