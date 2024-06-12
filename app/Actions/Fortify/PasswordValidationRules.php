<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Rules\Password;
use App\Rules\IsValidPassword;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return ['required', 'string', new IsValidPassword];
    }
}
