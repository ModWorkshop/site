<?php

namespace App\Http\Controllers;

use App\Models\SocialLogin;
use App\Models\User;
use App\Services\APIService;
use Arr;
use DB;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ViewErrorBag;
use Socialite;
use SplFileInfo;
use Storage;
use Str;
use FileEye\MimeMap\Type;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Log;
use Password;
use Throwable;

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
            'password' => ['required', APIService::getPasswordRule()],
            'avatar_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
        ]);

        if (User::where('email', $val['email'])->orWhere('unique_name', $val['unique_name'])->exists()) {
            abort(409);
        }

        $avatarFile = Arr::pull($val, 'avatar_file');
        
        $avatar = APIService::storeImage($avatarFile, 'users/avatars');

        $user = User::forceCreate([
            'name' => $val['name'],
            'unique_name' => $val['unique_name'],
            'email' => $val['email'],
            'password' => Hash::make($val['password']),
            'avatar' => $avatar['name'] ?? '',
        ]);

        event(new Registered($user));

        if (Auth::attempt(['email' => $val['email'], 'password' => $val['password']], true)) {
            $request->session()->regenerate();
            return $user;
        } else {
            Log::info('Failed to sign in');
        }

        return response('Something went wrong', Response::HTTP_BAD_REQUEST);

    }

    public function socialiteRedirect(string $provider)
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

        return $driver->redirect();
    }

    public function socialiteLogin(Request $request, string $provider)
    {
        $this->validateProvider($provider);
        $driver = Socialite::driver($provider);

        if ($provider !== 'steam') {
            $driver = $driver->stateless();
        }

        $providerUser = null;
        try {
            $providerUser = $driver->user();
        } catch (Throwable $e) {
            abort(400);
        }

        /** @var SocialLogin */
        $socialLogin = SocialLogin::where('social_id', $provider)->where('special_id', $providerUser->id)->first();

        $user = null;
        if (isset($socialLogin)) {
            $user = $socialLogin->user;
        } else {
            $name = $providerUser->name;
            $uniqueName = null;
            $avatar = $providerUser->avatar;

            if ($provider === 'steam') {
                $name = $providerUser->nickname;
                //Default is too small (64x64)
                $avatar = $providerUser->user['avatarfull'];
            } else if ($provider === 'github' || $provider === 'gitlab') {
                $uniqueName = $providerUser->nickname;
            }

            $name ??= 'Missing Name';

            //Download the avatar and store it in the site rather than "leeching" off the provider.
            $avatarFileName = null;
            if (isset($avatar)) {
                //TODO: enable exif extension in site
                $ext = null;
                if ($provider === 'github') {
                    //A little pain since the type is not in the given URL
                    $ext = (new Type(image_type_to_mime_type(exif_imagetype($avatar))))->getDefaultExtension();
                } else {
                    $ext = pathinfo($avatar, PATHINFO_EXTENSION);
                }
                //Same as hashName https://github.com/laravel/framework/blob/9.x/src/Illuminate/Http/FileHelpers.php#L48
                $avatarFileName = Str::random(40).'.'.$ext;
                Storage::disk('r2')->put('users/avatars/'.$avatarFileName, file_get_contents($avatar));
            }

            $uniqueName ??= $name;
            $uniqueName = preg_replace('([^a-zA-Z0-9-_])', '', strtolower($uniqueName));
            $users = User::where('unique_name', 'ILIKE', $uniqueName.'%')->get();

            //Try to make a unique name for the user
            $num = '';
            $found = false;
            while(!$found) {
                $current = $uniqueName.$num;
                if (!Arr::first($users, fn($val) => strtolower($val->unique_name) === $current)) {
                    $uniqueName = $current;
                    $found = true;
                } else {
                    $num ??= 0;
                    $num++;
                }
            }

            //Create a user
            $user = User::forceCreate([
                'name' => $name,
                'unique_name' => $uniqueName,
                'avatar' => $avatarFileName,
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
        if (!in_array($provider, ['steam', 'discord', 'github', 'gitlab'])) {
            abort(404);
        }
    }

    /**
     * Sends a password reset link that contains the token for resetPassword
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));
        # Is there a need to handle errors?
    }

    /**
     * The request that actually resets the password. After getting a token from /forgot-password
     */
    public function resetPassword(Request $request): string
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', APIService::getPasswordRule()],
        ]);
     
        $status = Password::reset($request->only('email', 'password', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
    
            $user->save();
    
            event(new PasswordReset($user));
        });
    
        return __($status);
    }

    /**
     * Returns whether or not the given token is valid
     */
    public function checkResetToken(Request $request): bool
    {
        $reset = DB::table('password_resets')->where(['email'=> $request->email])->first();
        if (!$reset) {
            return false;
        }
        
        return Hash::check($request->token, $reset->token);
    }
}
