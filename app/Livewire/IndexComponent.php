<?php

namespace App\Livewire;

use Livewire\Component;

class IndexComponent extends Component
{
    public function render()
    {
        return view('livewire.index-component')
            ->layout('layouts.app');
    }
}
