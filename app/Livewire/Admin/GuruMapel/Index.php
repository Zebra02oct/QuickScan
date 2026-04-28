<?php

namespace App\Livewire\Admin\GuruMapel;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
     use WithPagination;

    #[Layout('layouts.app')]
    #[Title('Manajemen Guru Mapel')]

    #[Url(history: true)]
    public $search = '';

    
    #[Url(history: true)]
    public $filter_kelas = '';
    
    #[Url(history: true)]
    public $filter_mapel = '';

  

    public function updated($property)
    {
        if (in_array($property, ['search', 'filter_kelas', 'filter_mapel'])) {
            $this->resetPage();
        }
    }
    public function render()
    {
         $applyFilters = function ($query) {
            if ($this->filter_kelas) {
                $query->where('kelas_id', $this->filter_kelas);
            }
            if ($this->filter_mapel) {
                $query->where('mapel_id', $this->filter_mapel);
            }
        };
         $gurus = Guru::with([
                'user', 
                'guruMapel' => function ($q) use ($applyFilters) {
                    $applyFilters($q);
                    $q->with(['mapel', 'kelas']);
                }
            ])
             ->whereHas('guruMapel', $applyFilters) 
            ->where(function ($query) {
                if ($this->search) {
                    $query->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('guruMapel', function ($q) {
                        $q->whereHas('mapel', function ($q2) {
                            $q2->where('nama_mapel', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('kelas', function ($q2) {
                            $q2->where('nama_kelas', 'like', '%' . $this->search . '%');
                        });
                    });
                }
            })
            ->paginate(10);
        return view('livewire.admin.guru-mapel.index', [
            'gurus'      => $gurus,
            'list_kelas' => Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(),
            'list_mapel' => Mapel::orderBy('nama_mapel')->get(),
        ]);
    }
}
