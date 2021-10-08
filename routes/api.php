<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EditModController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Mod;
use App\Models\Permission;
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
Route::get('categories/{category}', [CategoryController::class, 'getCategory']);
Route::get('categories', [CategoryController::class, 'getCategories']);
Route::get('tags', [TagController::class, 'getTags']);

Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class)->only(['index', 'show']);
Route::resource('files', FileController::class);
Route::resource('mods', ModController::class);
Route::resource('mods.comments', CommentController::class);
Route::resource('users', UserController::class)->except('store');

Route::middleware('can:view,file')->get('files/{file}/download', [FileController::class, 'downloadFile']);

// Routes that are protected under auth
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'currentUser']);
    
    //TODO: let only moderators do this
    Route::post('categories', [CategoryController::class, 'update']);

    Route::middleware('can:create,App\Mod')->group(function () {
        //Images
        Route::post('/mods/{mod}/images', [ModController::class, 'uploadModImage']);
        Route::delete('/mods/{mod}/images/{image}', [ModController::class, 'deleteModImage']);
    
        //Files
        Route::post('/mods/{mod}/files', [ModController::class, 'uploadModFile']);
        Route::delete('/mods/{mod}/files/{file}', [ModController::class, 'deleteModFile']);
    });
});

/**
 * @group Category
 */
Route::prefix('games')->group(function () {
    Route::get('/', [CategoryController::class, 'getGames']);
    Route::get('/{game}', [CategoryController::class, 'getGame']);
    Route::get('/{game}/categories', [CategoryController::class, 'getCategories']);
});

//blabla