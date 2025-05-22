<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdSlot extends Component
{
    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function render()
    {
        return view('components.ad-slot');
    }
} 