<?php

namespace App\Livewire\Dashboard\Guru;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
        #[Layout('layouts.app')]
    #[Title('Dashboard Guru')]
    public function render()
    {
        return view('livewire.dashboard.guru.index');
    }
}
