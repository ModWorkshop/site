<?php

namespace App\Http\Controllers;

use App\Models\SocialLogin;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Socialite;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * @group Social Logins
 */
class SocialLoginController extends Controller
{
    public function __construct() {

    }

    /**
     * List social logins
     */
    public function index()
    {
        return $this->user()->socialLogins;
    }

    public function linkAccountRedirect(string $provider)
    {
        $this->validateProvider($provider);
        $driver = Socialite::driver($provider);

        if ($provider !== 'steam') {
            $driver = $driver->stateless(); //For some reason Steam doesn't like this but the others don't like the opposite ¯\_(ツ)_/¯
        }

        if ($provider === 'discord') {
            $driver = $driver->setScopes(['identify']);
        } else if ($provider === 'github') {
            $driver = $driver->setScopes(['read:user']);
        }

        return $driver->redirectUrl(env('SOCIALITE_LINK_REDIRECT_URL').$provider)->redirect();
    }

    public function linkAccountCallback(string $provider)
    {
        $this->validateProvider($provider);
        $driver = Socialite::driver($provider)->redirectUrl(env('SOCIALITE_LINK_REDIRECT_URL').$provider);

        if ($provider !== 'steam') {
            $driver = $driver->stateless();
        }

        $providerUser = null;

        try {
            $providerUser = $driver->user();
        } catch (Throwable $e) {
            abort(400);
        }

        $user = $this->user();

        /** @var SocialLogin */
        if (SocialLogin::where('social_id', $provider)->where(fn($q) => $q->where('user_id', $user->id)->orWhere('special_id', $providerUser->id))->exists()) {
            abort(409, 'The account is already linked!');
        } else {
            //Create a social login so the user can login with it later
            SocialLogin::create([
                'social_id' => $provider,
                'special_id' => $providerUser->id,
                'user_id' => $user->id
            ]);

            $user->activated = true;
            $user->save();
        }
    }

    public function destroy(string $provider)
    {
        $this->validateProvider($provider);
        $user = $this->user();

        $socialLogin = SocialLogin::where('social_id', $provider)->where('user_id', $user->id)->firstOrFail();

        $count = $user->socialLogins()->count();
        if ($count < 1) {
            if (!$user->signable) {
                abort(405, 'Cannot remove last provider when account has no email and password setup!');
            }

            $user->activated = false;
            $user->save();
        }


        $socialLogin->delete();
    }

    public function validateProvider(string $provider)
    {
        if (!in_array($provider, ['steam', 'discord', 'github', 'gitlab'])) {
            abort(404);
        }
    }
}
