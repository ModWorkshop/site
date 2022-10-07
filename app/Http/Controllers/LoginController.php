<?php

namespace App\Http\Controllers;

use App\Models\SocialLogin;
use App\Models\User;
use App\Services\APIService;
use Arr;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\Rules\Password;
use Socialite;

/**
 * @group Authentication
 */
class LoginController extends Controller
{
    public function __construct() {
        
    }
    /**
     * Login
     * 
     * Attempts to login a user with the provided username and password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $val = $request->validate([
            'email' => 'required|email', //blabla@email.com
            'password' => ['required'],
            'remember' => 'boolean'
        ]);

        if (Auth::attempt(['email' => $val['email'], 'password' => $val['password']], $val['remember'])) {
            $request->session()->regenerate();
            return response('');
        } else {
            return response('Email or password are incorrect', Response::HTTP_UNAUTHORIZED);
        }

        return response('Something went wrong', Response::HTTP_BAD_REQUEST);
    }

    /**
     * Logout
     * 
     * Logs out the currently authenticated user.
     *
     * @authenticated
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Register
     * 
     * Attempts to register users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $val = $request->validate([
            'name' => ['required'],
            'unique_name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(12)->numbers()->mixedCase()->uncompromised()],
            'avatar_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
        ]);

        if (User::where('email', $val['email'])->orWhere('unique_name', $val['unique_name'])->exists()) {
            abort(409);
        }

        $avatarFile = Arr::pull($val, 'avatar_file');
        
        $avatar = APIService::tryUploadFile($avatarFile, 'users/avatars') ?? '';

        $user = User::create([
            'name' => $val['name'],
            'unique_name' => $val['unique_name'],
            'email' => $val['email'],
            'password' => Hash::make($val['password']),
            'avatar' => $avatar,
        ]);

        if (Auth::attempt(['email' => $val['email'], 'password' => $val['password']], true)) {
            $request->session()->regenerate();
            return $user;
        }

        return response('Something went wrong', Response::HTTP_BAD_REQUEST);

    }

    public function socialiteRedirect(string $provider)
    {
        $this->validateProvider($provider);
        $driver = Socialite::driver($provider)->stateless();
        if ($provider === 'discord') {
            $driver = $driver->setScopes(['identify']);
        } else if ($provider === 'github') {
            $driver = $driver->setScopes(['read:user']);
        }

        return $driver->redirect();
    }

    public function socialiteLogin(Request $request, string $provider)
    {
        $this->validateProvider($provider);
        $providerUser = Socialite::driver($provider)->stateless()->user();
    
        /** @var SocialLogin */
        $socialLogin = SocialLogin::where('social_id', $provider)->where('special_id', $providerUser->id)->first();

        $user = null;
        if (isset($socialLogin)) {
            $user = $socialLogin->user;
        } else {
            //Create a user
            $user = User::create([
                'name' => $providerUser->name,
                'avatar' => $providerUser->avatar,
            ]);
    
            //Create a social login so the user can login with it later
            SocialLogin::create([
                'social_id' => $provider,
                'special_id' => $providerUser->id,
                'user_id' => $user->id
            ]);
        }
    
        //Attention: this only runs AFTER we verify the user has logged in. This data is returned by the provider, therefore we can safely login the user.
        if (Auth::login($user, true)) {
            $request->session()->regenerate();
        }
    }

    public function validateProvider(string $provider)
    {
        if (!in_array($provider, ['steam', 'discord', 'github'])) {
            abort(404);
        }
    }
}
