<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\IconCaptchaSetting;

class IconCaptchaDisplay extends Component
{
    public $uniqueId;
    public $enabled;
    public $theme;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($uniqueId)
    {
        $this->uniqueId = $uniqueId;
        $this->enabled = IconCaptchaSetting::isEnabled('enabled', true);
        $this->theme = IconCaptchaSetting::get('theme', 'light');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.icon-captcha-display');
    }
}
}