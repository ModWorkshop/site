<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Ban;
use App\Models\SocialLogin;
use App\Models\User;
use App\Models\UserCase;
use App\Models\UserRecord;
use App\Services\APIService;
use App\Services\Utils;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ViewErrorBag;
use nickurt\StopForumSpam\Rules\IsSpamEmail;
use nickurt\StopForumSpam\Rules\IsSpamUsername;
use Socialite;
use SplFileInfo;
use StopForumSpam;
use Storage;
use Str;
use FileEye\MimeMap\Type;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\UploadedFile;
use Log;
use Password;
use Throwable;
use Jcupitt\Vips;

use const App\Services\animated;

/**
 * @group Authentication
 */
class LoginController extends Controller
{
    public function __construct() {

    }
    /**
     * Login user
     *
     * Attempts to login a user with the provided username and password
     */
    public function login(Request $request)
    {
        $val = $request->validate([
            'email' => 'required|email', //blabla@email.com
            'password' => ['required', 'max:128'],
            'remember' => 'boolean',
        ]);

        APIService::checkCaptcha($request);

        if (Auth::attempt(['email' => $val['email'], 'password' => $val['password']], $val['remember'])) {
            $request->session()->regenerate();
            return response('');
        } else {
            return response('Email or password are incorrect', Response::HTTP_UNAUTHORIZED);
        }

        return response('Something went wrong', Response::HTTP_BAD_REQUEST);
    }

    /**
     * Logout user
     *
     * Logs out the currently authenticated user.
     *
     * @authenticated
     */
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Register user
     *
     * Attempts to register users
     */
    public function register(Request $request)
    {
        $val = $request->validate([
            'name' => ['required'],
            'unique_name' => ['alpha_dash:ascii', 'not_regex:/^\d+$/', 'nullable', 'min:3', 'max:50'],
            'email' => ['required', 'email', new \nickurt\StopForumSpam\Rules\IsSpamEmail(2)],
            'password' => ['required', APIService::getPasswordRule(), 'max:128'],
            'avatar_file' => 'nullable|max:512000|mimes:png,webp,avif,gif,jpg',
        ]);

        try {
            if (StopForumSpam::setIp($request->ip)->isSpamIp()) {
            abort(400);
        }
        } catch (\Exception $e) {

        }

        if (APIService::containsSpammyWords($val['name']) || APIService::containsSpammyWords($val['unique_name'])) {
            abort(400);
        };

        APIService::checkCaptcha($request);

        if (User::where('email', $val['email'])->orWhere(DB::raw('LOWER(unique_name)'), Str::lower($val['unique_name']))->exists()) {
            abort(409);
        }

        $avatarFile = Arr::pull($val, 'avatar_file');

        $avatar = APIService::storeImage($avatarFile, 'users/images', null, [
            'size' => 256,
            'thumbnailSize' => 64
        ]);

        // Skip verification on dev
        $shouldVerify = app()->isLocal() && empty(env('MAIL_HOST'));

        $user = User::forceCreate([
            'name' => $val['name'],
            'unique_name' => Str::lower($val['unique_name']),
            'email' => $val['email'],
            'password' => Hash::make($val['password']),
            'last_online' => Carbon::now(),
            'avatar' => $avatar['name'] ?? '',
            'avatar_has_thumb' => isset($avatar['name']),
            'email_verified_at' => $shouldVerify ? Carbon::now() : null,
            'activated' => $shouldVerify,
        ]);

        event(new Registered($user));

        //Attempt restore (for bans/warnings)
        $record = UserRecord::where('email', $val['email'])->first();
        if (isset($record)) {
            Utils::partlyRestoreUser($record, $user->id);
        }

        // 3.5.3 Disabled auto login to make it harder for spam bots
        if (Auth::validate(['email' => $val['email'], 'password' => $val['password']])) {
            return new UserResource($user->refresh());
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

        return $driver->redirect(env('SOCIALITE_REDIRECT_URL').$provider);
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
            \Log::error($e);
            abort(400);
        }

        /** @var SocialLogin */
        $socialLogin = SocialLogin::where('social_id', $provider)->where('special_id', $providerUser->id)->first();

        $user = null;
        if (isset($socialLogin)) {
            $user = $socialLogin->user;
        } else {
            $name = $providerUser->nickname ?? $providerUser->name;
            $uniqueName = $providerUser->name ?? $name;
            $avatar = $providerUser->avatar;

            if ($provider === 'steam') {
                //Default is too small (64x64)
                $avatar = $providerUser->user['avatarfull'];
            }

            $name ??= 'Missing Name';

            //Download the avatar and store it in the site rather than "leeching" off the provider.
            $avatarFileName = null;
            if (isset($avatar)) {
                $ext = null;
                if ($provider === 'github') {
                    //A little pain since the type is not in the given URL
                    $ext = (new Type(image_type_to_mime_type(exif_imagetype($avatar))))->getDefaultExtension();
                } else {
                    $ext = pathinfo($avatar, PATHINFO_EXTENSION);
                }

                $buffer = file_get_contents($avatar);
                if (!empty($buffer)) {
                    $opts = isset(animated[$ext]) ? '[n=-1]' : '';
                    [ 'name' => $avatarFileName ] = APIService::storeImageByObject(Vips\Image::newFromBuffer($buffer, $opts), 'users/images', null, [
                        'size' => 256,
                        'thumbnailSize' => 64
                    ]);
                }
            }

            $uniqueName = preg_replace('([^a-zA-Z0-9-_])', '', strtolower($uniqueName));
            $users = User::where('unique_name', 'ILIKE', $uniqueName.'%')->get();
            $uniqueName ??= 'unknown';

            //Try to make a unique name for the user
            $num = '';
            $found = false;
            while(!$found) {
                $current = $uniqueName.$num;
                if (!Arr::first($users, fn($val) => Str::lower($val->unique_name) === $current)) {
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
                'unique_name' => Str::lower($uniqueName),
                'avatar' => $avatarFileName,
                'last_online' => Carbon::now(),
                'activated' => true
            ]);

            //Create a social login so the user can login with it later
            SocialLogin::create([
                'social_id' => $provider,
                'special_id' => $providerUser->id,
                'user_id' => $user->id
            ]);

            //Attempt restore (for bans/warnings)
            $record = UserRecord::whereRaw("social_logins->>? = ?", [$provider, $providerUser->id])->first();
            if (isset($record)) {
                Utils::partlyRestoreUser($record, $user->id);
            }
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
        $val = $request->validate(['email' => 'required|email|max:255']);
        $email = ['email' => Str::lower($val['email'])];
        Password::sendResetLink($email);
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
            'password' => ['required', APIService::getPasswordRule(), 'max:128'],
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
}
