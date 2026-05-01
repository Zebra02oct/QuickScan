<?php

namespace App\Livewire\Dashboard\Siswa;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
        #[Layout('layouts.app')]
    #[Title('Dashboard Siswa')]
    public function render()
    {
        return view('livewire.dashboard.siswa.index');
    }
}
