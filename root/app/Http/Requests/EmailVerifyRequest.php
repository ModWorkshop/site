<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Http\FormRequest;

class EmailVerifyRequest extends EmailVerificationRequest
{
    public function fulfill()
    {
        /** @var User */
        $user = $this->user();

        if (!$user->hasVerifiedEmail() || isset($user->pending_email)) {
            $user->verifyPendingEmail();
            event(new Verified($user));
        }
    }
}
