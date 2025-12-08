<?php

namespace App\Modules\Main\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Regex pattern to allow letters, spaces, periods, hyphens, and underscores
        if (!preg_match('/^[A-Za-z\s.-]+(?:\s[A-Za-z\s.-]+)*$/', $value)) {
            $fail("The :attribute field must only contain letters, spaces, periods (.) and hyphens (-).");
        }

    }
}
