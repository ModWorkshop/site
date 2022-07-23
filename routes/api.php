<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EditModController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Mod;
use App\Models\Permission;
use App\Models\SocialLogin;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Telescope\Http\Controllers\ModelsController;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout']);

/**
 * @hideFromAPIDocumentation
 */
Route::get('/auth/steam/redirect', function(Request $request) {
    return Socialite::driver('steam')->redirect();
});

/**
 * @hideFromAPIDocumentation
 */
Route::get('/auth/steam/callback', function(Request $request) {
    $steamUser = Socialite::driver('steam')->user();

    /**
     * @var SocialLogin
     */
    $socialLogin = SocialLogin::where('social_id', 'steam')->where('special_id', $steamUser->id)->first();
    $user = null;
    if (isset($socialLogin)) {
        $user = $socialLogin->user;
    } else {
        //Create a user
        $user = User::create([
            'name' => $steamUser->nickname,
            'avatar' => $steamUser->avatar,
        ]);

        //Create a social login so the user can login with it later
        SocialLogin::create([
            'social_id' => 'steam',
            'special_id' => $steamUser->id,
            'user_id' => $user->id
        ]);

    }

    //Attention: this only runs AFTER we verify the user has logged in. This data is returned by Steam, therefore we can safely login the user.
    if (Auth::login($user, true)) {
        $request->session()->regenerate();
    }

    // return redirect('http://localhost:3000');
});

// https://laravel.com/docs/8.x/authorization#middleware-actions-that-dont-require-models
// Resources
Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class)->only(['index', 'show']);
Route::resource('files', FileController::class);
Route::resource('mods', ModController::class);
Route::resource('tags', TagController::class);
/**
 * @group Mods
 */
Route::resource('mods.comments', CommentController::class);
Route::resource('users', UserController::class)->except(['store', 'show']);
Route::get('users/{user}', [UserController::class, 'getUser'])->where('user', '[0-9a-zA-Z\-_]+');

Route::resource('categories', CategoryController::class);
Route::resource('games', GameController::class);
Route::get('games/{game}', [GameController::class, 'getGame'])->where('game', '[0-9a-z\-]+');
Route::get('games/{game}/categories', [CategoryController::class, 'index']);

Route::middleware('can:view,file')->get('files/{file}/download', [FileController::class, 'downloadFile']);

// Routes that are protected under auth
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'currentUser']);
    
    Route::middleware('can:create,App\Mod')->group(function () {
        //Images
        Route::post('/mods/{mod}/images', [ModController::class, 'uploadModImage']);
        Route::delete('/mods/{mod}/images/{image}', [ModController::class, 'deleteModImage']);
    
        //Files
        Route::post('/mods/{mod}/files', [ModController::class, 'uploadModFile']);
        Route::delete('/mods/{mod}/files/{file}', [ModController::class, 'deleteModFile']);
    });
});