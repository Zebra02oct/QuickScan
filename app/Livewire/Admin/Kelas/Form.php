<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Form extends Component
{
    public $kelas_id = null;
    public $tingkat = '';
    public $jurusan = '';
    public $rombel = ''; 
    public $nama_kelas = '';
    public $guru_id = ''; 
    
    public $is_active = 1; 

    public $original_tingkat;
    public $original_jurusan;
    public $original_rombel;
    
   
    public $original_is_active; 

    protected function rules()
    {
        return [
            'tingkat' => 'required|string',
            'jurusan' => 'required|string',
            'rombel' => 'nullable|string',
         'guru_id' => 'required|exists:gurus,id',
            'is_active' => 'required|boolean', 
            'nama_kelas' => [
                'required',
                'string',
                Rule::unique('kelas', 'nama_kelas')
                    ->ignore($this->kelas_id),
            ],
        ];
    }

    protected $messages = [
        'nama_kelas.unique' => 'Nama Kelas ini sudah terdaftar! Silakan cek kembali.',
        'tingkat.required'  => 'Tingkat wajib dipilih.',
        'jurusan.required'  => 'Jurusan wajib diisi.',
        'is_active.required' => 'Status kelas wajib dipilih.',

        'guru_id.required'  => 'Wali Kelas wajib dipilih.', 
    'guru_id.exists'    => 'Data Wali Kelas tidak valid/tidak ditemukan.',
    ];

  #[Computed]
public function listGuru()
{
    return Guru::with('user')
        ->where(function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('status', 'aktif');
            });
            if ($this->guru_id) {
                $query->orWhere('id', $this->guru_id);
            }
        })
        ->get()
        ->sortBy(function ($guru) {
            return $guru->user->name ?? 'ZZZ'; 
        });
}

    public function updated($property)
    {
        if (in_array($property, ['tingkat', 'jurusan', 'rombel'])) {
            $this->nama_kelas = trim("{$this->tingkat} {$this->jurusan} {$this->rombel}");
        }
        $this->validateOnly($property);
    }

    public function loadData($id = null)
    {
        $this->reset();
        $this->resetValidation();
    
        $this->is_active = 1; 

        if ($id) {
            $data = Kelas::find($id);
            if ($data) {
                $this->kelas_id = $data->id;
                $this->tingkat = $data->tingkat;
               $this->guru_id = $data->guru_id;
                $this->jurusan = $data->jurusan;
                
                // Set Status
                $this->is_active = $data->is_active;
                $this->original_is_active = $data->is_active;
              
                $nama_parts = explode(' ', $data->nama_kelas);
                $this->rombel = end($nama_parts); 
                
                $this->nama_kelas = $data->nama_kelas;

                $this->original_tingkat = $data->tingkat;
                $this->original_jurusan = $data->jurusan;
                $this->original_rombel = end($nama_parts);

                
            }
        }
    }

    public function save()
    {
        $this->nama_kelas = strtoupper(trim("{$this->tingkat} {$this->jurusan} {$this->rombel}"));   
        $this->validate();

        Kelas::updateOrCreate(
            ['id' => $this->kelas_id],
            [
                'tingkat'    => $this->tingkat,
                'jurusan'    => strtoupper(trim($this->jurusan)),
                'nama_kelas' => $this->nama_kelas,
                'guru_id'    => $this->guru_id ?: null, 
                'is_active'  => $this->is_active,
            ]
        );

        $this->dispatch('close-modal');
        $this->dispatch('refresh-kelas');
        
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text'  => $this->kelas_id ? 'Data Kelas berhasil diperbarui' : 'Kelas baru berhasil ditambahkan'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.kelas.form');
    }
}