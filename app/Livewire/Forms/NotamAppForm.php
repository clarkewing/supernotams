<?php

namespace App\Livewire\Forms;

use App\Rules\IcaoFplRule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NotamAppForm extends Form
{
    #[Validate(as: 'flight plan message')]
    public string $fpl = '';

    public function rules(): array
    {
        return [
            'fpl' => [
                'required',
                'string',
                'min:5',
                new IcaoFplRule,
            ],
        ];
    }
}
