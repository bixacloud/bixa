<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FroalaEditor extends Component
{
    /**
     * Editor ID
     *
     * @var string
     */
    public $id;

    /**
     * Editor name attribute
     *
     * @var string
     */
    public $name;

    /**
     * Editor content
     *
     * @var string
     */
    public $content;

    /**
     * Editor height
     *
     * @var int
     */
    public $height;

    /**
     * Editor placeholder
     *
     * @var string
     */
    public $placeholder;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $id = 'froala-editor',
        $name = 'content',
        $content = '',
        $height = 300,
        $placeholder = 'Write your content here...'
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->height = $height;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.froala-editor');
    }
}