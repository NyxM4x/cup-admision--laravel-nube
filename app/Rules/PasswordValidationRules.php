<?php

namespace App\Rules;

use Illuminate\Validation\Rules;

class PasswordValidationRules
{
    public static function password(): Rules\Password
    {
        return Rules\Password::min(8)
            ->mixedCase()
            ->symbols();
    }
}
