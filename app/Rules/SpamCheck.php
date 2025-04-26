<?php

namespace App\Rules;

use App\Services\APIService;
use Auth;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SpamCheck implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $splt = explode(':', $attribute);
        if (isset($splt[1]) && $splt[1] == '') {
            $user = Auth::user();
            $trustLevel = $user->getTrustLevel();
            if ($trustLevel < 12) {
                if ($user->getAccountAgeInHours() < 1 && APIService::countLinks($value) > 0) {
                    $fail('New accounts cannot post links!');
                }
                elseif (APIService::checkSpamContent($value)) {
                    abort(422, ':attribute contains spam content!');
                }
            }
        }
    }
}
