<?php

namespace LaravelCms\View\Components;

use Illuminate\View\Component;

class GridTable extends Component
{
    /**
     * Grid instance
     */
    public $grid;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($grid)
    {
        $this->grid = $grid;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('cms::grid.table');
    }
}
