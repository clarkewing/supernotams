<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IcaoFplRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! str_contains($value, 'DOF/') || ! str_contains($value, 'REG/')) {
            $fail('The :attribute must be a valid ICAO flight plan.');
        }
    }
}
