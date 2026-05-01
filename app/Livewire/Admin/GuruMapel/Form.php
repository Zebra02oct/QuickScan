<?php

namespace App\Livewire\Admin\GuruMapel;

use App\Models\Guru;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\Mapel;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Form extends Component
{
    public $guru_mapel_id = null;
    public $guru_id = '';
    public $mapel_id = '';
    public $kelas_id = '';

    protected function rules()
    {
        return [
            'guru_id'  => 'required|exists:gurus,id',
            'mapel_id' => 'required|exists:mapels,id',
            'kelas_id' => 'required|exists:kelas,id',
        ];
    }

    protected $messages = [
        'guru_id.required'  => 'Guru wajib dipilih.',
        'mapel_id.required' => 'Mata Pelajaran wajib dipilih.',
        'kelas_id.required' => 'Kelas wajib dipilih.',
    ];

    #[Computed]
    public function listGuru()
    {
        return Guru::with('user')->get()->sortBy('user.name');
    }

    #[Computed]
    public function listMapel()
    {
        return Mapel::orderBy('nama_mapel')->get();
    }

    #[Computed]
    public function listKelas()
    {
        return Kelas::orderBy('tingkat', 'desc')->orderBy('nama_kelas')->get();
    }

    public function loadData($id = null)
    {
        $this->reset();
        $this->resetValidation();

        if ($id) {
            $data = GuruMapel::find($id);
            if ($data) {
                $this->guru_mapel_id = $data->id;
                $this->guru_id       = $data->guru_id;
                $this->mapel_id      = $data->mapel_id;
                $this->kelas_id      = $data->kelas_id;
            }
        }
    }

    public function save()
    {
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