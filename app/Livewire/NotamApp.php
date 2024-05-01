<?php

namespace App\Livewire;

use App\Actions\ParseFlightPlan;
use App\DTO\AtsMessage;
use App\Livewire\Forms\NotamAppForm;
use App\Models\Flight;
use App\View\Components\AppLayout;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout(AppLayout::class, ['title' => 'App'])]
class NotamApp extends Component
{
    public NotamAppForm $form;

    public function process()
    {
        $this->validate();

        $flight = Flight::fromFpl(
            AtsMessage::from($this->form->fpl)
        );

        dd($flight);
    }

    public function render(): View
    {
        return view('livewire.notam-app');
    }
}
