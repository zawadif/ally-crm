<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueConcatenatedPhoneNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $countryCodeField;
    private $phoneNumberField;
    public function __construct($countryCodeField, $phoneNumberField)
    {
        $this->countryCodeField = $countryCodeField;
        $this->phoneNumberField = $phoneNumberField;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $countryCode = request($this->countryCodeField);
        $phoneNumber = request($this->phoneNumberField);
        $concatenatedValue = "+".$countryCode . $phoneNumber;

        // Perform your validation logic on the concatenated value
        // Example: Check if the concatenated value is unique in the database

        $count = User::where('phoneNumber', $concatenatedValue)
            ->count();

        // Return true if the count is zero (unique), false otherwise
        return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The phone number already exists.';
    }
}
