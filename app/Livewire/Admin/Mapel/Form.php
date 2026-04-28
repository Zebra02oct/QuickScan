<?php

namespace App\Livewire\Admin\Mapel;

use App\Models\Mapel;
use Livewire\Component;

class Form extends Component
{
    public $mapel_id = null;
    public $kode_mapel = '';
    public $nama_mapel = '';
    public $kategori = 'umum'; 

    protected function rules()
    {
        return [
            'nama_mapel' => 'required|string|max:255',
            'kategori'   => 'required|in:umum,kejuruan',
          
            'kode_mapel' => 'required|string|max:50|unique:mapels,kode_mapel,' . $this->mapel_id,
        ];
    }

    protected $messages = [
        'kode_mapel.unique'   => 'Kode Mapel ini sudah dipakai! Silakan ganti yang lain.',
        'kode_mapel.required' => 'Kode Mapel wajib diisi.',
        'nama_mapel.required' => 'Nama Mata Pelajaran wajib diisi.',
        'kategori.required'   => 'Kategori Mapel wajib dipilih.',
    ];

public function generateKode()
    {
        if (empty($this->nama_mapel)) {
            $this->kode_mapel = '';
            return;
        }

        $cleanName = trim($this->nama_mapel);
        $words = explode(' ', $cleanName);
        $prefix = '';

        if (count($words) >= 2) {
            foreach ($words as $word) {
                if (!empty($word)) {
                    $prefix .= strtoupper(substr($word, 0, 1));
                }
            }
            $prefix = substr($prefix, 0, 4);
        } else {
            $prefix = strtoupper(substr($cleanName, 0, 3));
        }

    
        $kategoriKode = ($this->kategori === 'kejuruan') ? 'K' : 'U'; 
    
        $baseKode = $prefix . '-' . $kategoriKode;

     
        $query = \App\Models\Mapel::where('kode_mapel', 'like', $baseKode . '%');
        if ($this->mapel_id) {
            $query->where('id', '!=', $this->mapel_id);
        }
        $lastRecord = $query->orderBy('kode_mapel', 'desc')->first();

        if ($lastRecord) {

            $parts = explode('-', $lastRecord->kode_mapel);
            $lastNumber = isset($parts[2]) ? (int)$parts[2] : 1;
            
            $this->kode_mapel = $baseKode . '-' . ($lastNumber + 1);
        } else {
        
            $this->kode_mapel = $baseKode;
        }
    }

    public function updatedNamaMapel($value)
    {
        $this->generateKode();
        $this->validateOnly('nama_mapel');
    }

    public function updatedKategori($value)
    {
        $this->generateKode();
        $this->validateOnly('kategori');
    }

    public function updatedKodeMapel($value)
    {
        $this->kode_mapel = strtoupper(str_replace(' ', '', $value));
    }

    public function loadData($id = null)
    {
        $this->reset();
        $this->resetValidation();

        if ($id) {
            $data = Mapel::find($id);
            if ($data) {
                $this->mapel_id   = $data->id;
                $this->kode_mapel = $data->kode_mapel;
                $this->nama_mapel = $data->nama_mapel;
                $this->kategori   = $data->kategori;
            }
        }
    }

    public function save()
    {
     
        $this->kode_mapel = strtoupper(str_replace(' ', '', $this->kode_mapel));
        
        $this->validate();

        Mapel::updateOrCreate(
            ['id' => $this->mapel_id],
            [
                'kode_mapel' => $this->kode_mapel,
            'nama_mapel' => strtolower(trim($this->nama_mapel)),
                'kategori'   => $this->kategori,
            ]
        );

        $this->dispatch('close-modal');
        $this->dispatch('refresh-mapel');
        
        $this->dispatch('swal:success', [
            'title' => 'Mantap!',
            'text'  => 'Data Mata Pelajaran berhasil disimpan.'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.mapel.form');
    }
}