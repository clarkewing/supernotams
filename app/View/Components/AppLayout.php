<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppLayout extends Component
{
    public function __construct(
        public ?string $title = null,
    ) {
    }

    public function pageTitle()
    {
        if (is_null($this->title)) {
            return config('app.name', 'SuperNOTAMs');
        }

        return $this->title.' - '.config('app.name', 'SuperNOTAMs');
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
