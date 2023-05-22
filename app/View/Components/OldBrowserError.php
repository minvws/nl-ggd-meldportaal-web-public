<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OldBrowserError extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function render()
    {
        return view('components.old-browser-error');
    }
}
