<?php

use App\Http\Controllers\EditModController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

Route::get('/auth/steam/redirect', function(Request $request) {
    return Socialite::driver('steam')->redirect();
});

Route::get('/auth/steam/callback', function(Request $request) {
    $user = Socialite::driver('steam')->user();
    return json_encode($user);
});

// https://laravel.com/docs/8.x/authorization#middleware-actions-that-dont-require-models
// Routes that are protected under auth
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('can:create,App\Mod')->post('/mod', [EditModController::class, 'save']);
    Route::post('/user/{id}/avatar', [UserSettingsController::class, 'uploadAvatar']);
    Route::get('/user', fn (Request $request) => $request->user());
});