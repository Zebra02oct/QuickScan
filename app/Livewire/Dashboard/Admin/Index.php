<?php

namespace App\Livewire\Dashboard\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard Admin')]

    public function render()
    {
        return view('livewire.dashboard.admin.index');
    }
}
