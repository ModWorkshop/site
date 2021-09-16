<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EditModController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSettingsController;
use App\Models\Category;
use App\Models\Mod;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Telescope\Http\Controllers\ModelsController;

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
    $user = Socialite::driver('steam')->user();
    return json_encode($user);
});

// https://laravel.com/docs/8.x/authorization#middleware-actions-that-dont-require-models
Route::get('users/{user}', [UserController::class, 'getUser']);
Route::get('categories/{category}', [CategoryController::class, 'getCategory']);
Route::get('categories', [CategoryController::class, 'getCategories']);
Route::get('mods', [ModController::class, 'getMods']);
Route::get('mods/{mod}', [ModController::class, 'getMod']);
Route::get('tags', [TagController::class, 'getTags']);


// Routes that are protected under auth
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users/{id}/avatar', [UserSettingsController::class, 'uploadAvatar']);
    Route::get('/user', [UserController::class, 'currentUser']);
    
    //TODO: let only moderators do this
    Route::post('categories', [CategoryController::class, 'update']);
    

    Route::middleware('can:create,App\Mod')->post('/mods', [ModController::class, 'create']);
    Route::middleware('can:create,App\Mod')->patch('/mods/{mod}', [ModController::class, 'update']);
    Route::middleware('can:create,App\Mod')->post('/mods/{mod}/images', [ModController::class, 'uploadModImage']);
    Route::middleware('can:create,App\Mod')->delete('/mods/{mod}/images/{image}', [ModController::class, 'deleteModImage']);
});

/**
 * @group Category
 */
Route::prefix('games')->group(function () {
    Route::get('/', [CategoryController::class, 'getGames']);
    Route::get('/{game}', [CategoryController::class, 'getGame']);
    Route::get('/{game}/categories', [CategoryController::class, 'getCategories']);
});