<?php

use App\Http\Controllers\BanController;
use App\Http\Controllers\BlockedTagController;
use App\Http\Controllers\BlockedUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FollowedGameController;
use App\Http\Controllers\FollowedModController;
use App\Http\Controllers\FollowedUserController;
use App\Http\Controllers\ForumCategoryController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModCommentsController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\ModMemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ThreadCommentsController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserController;
use App\Models\SocialLogin;
use App\Models\User;
use Illuminate\Http\Request;
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

/**
 * @group Mods
 */
Route::resource('mods.files', FileController::class);
Route::middleware('can:update,mod')->group(function() {
    Route::delete('mods/{mod}/files', [FileController::class, 'deleteAllFiles']);
    //Images
    Route::post('mods/{mod}/images', [ModController::class, 'uploadModImage']);
    Route::delete('mods/{mod}/images/{image}', [ModController::class, 'deleteModImage']);
    Route::delete('mods/{mod}/images', [ModController::class, 'deleteAllImages']);
});

Route::middleware('can:super-update,mod')->group(function() {
    Route::patch('mods/{mod}/owner', [ModController::class, 'transferOwnership']);
    Route::patch('mods/{mod}/transfer-request/cancel', [ModController::class, 'cancelTransferRequest']);
});

Route::middleware('can:view,file')->get('files/{file}/download', [FileController::class, 'downloadFile']);

//General mods
Route::resource('mods.links', LinkController::class);
Route::resource('mods.members', ModMemberController::class)->only(['store', 'destroy', 'update']);
Route::patch('mods/{mod}/members/{member}/accept', [ModMemberController::class, 'accept']);
Route::patch('mods/{mod}/transfer-request/accept', [ModController::class, 'acceptTransferRequest']);
Route::resource('mods', ModController::class);
Route::get('mods/followed', [ModController::class, 'followed']);
Route::post('mods/{mod}/register-view', [ModController::class, 'registerView']);
Route::post('mods/{mod}/register-download', [ModController::class, 'registerDownload']);
Route::resource('mods.comments', ModCommentsController::class);
Route::get('mods/{mod}/comments/{comment}/page', [ModCommentsController::class, 'page']);
Route::middleware('can:suspend,mod')->patch('mods/{mod}/suspended', [ModController::class, 'suspend']);
Route::middleware('auth:sanctum')->group(function() {
    Route::middleware('can:like,mod')->post('mods/{mod}/toggle-liked', [ModController::class, 'toggleLike']);
    Route::post('mods/{mod}/comments/subscription', [ModCommentsController::class, 'subscribe']);
    Route::delete('mods/{mod}/comments/subscription', [ModCommentsController::class, 'unsubscribe']);
    Route::post('mods/{mod}/comments/{comment}/subscription', [ModCommentsController::class, 'subscribeComment']);
    Route::delete('mods/{mod}/comments/{comment}/subscription', [ModCommentsController::class, 'unsubscribeComment']);
});

//Games/categories/tags
Route::resource('categories', CategoryController::class);
Route::resource('games', GameController::class);
Route::get('games/{game}', [GameController::class, 'getGame'])->where('game', '[0-9a-z\-]+');
Route::get('games/{game}/categories', [CategoryController::class, 'index']);
Route::resource('tags', TagController::class);

/**
 * @group Forums
 */
Route::resource('forums', ForumController::class)->only(['index', 'show', 'update']);
Route::resource('forum-categories', ForumCategoryController::class);
Route::resource('threads', ThreadController::class);
Route::resource('threads.comments', ThreadCommentsController::class);
Route::get('threads/{thread}/comments/{comment}/page', [ThreadCommentsController::class, 'page']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('threads/{thread}/comments/subscription', [ThreadCommentsController::class, 'subscribe']);
    Route::delete('threads/{thread}/comments/subscription', [ThreadCommentsController::class, 'unsubscribe']);
    Route::post('threads/{thread}/comments/{comment}/subscription', [ThreadCommentsController::class, 'subscribeComment']);
    Route::delete('threads/{thread}/comments/{comment}/subscription', [ThreadCommentsController::class, 'unsubscribeomment']);
});

/**
 * @group Users
 */
Route::resource('users', UserController::class)->except(['store', 'show']);
Route::resource('bans', BanController::class);
Route::resource('notifications', NotificationController::class)->only(['index', 'store', 'destroy', 'update']);
Route::middleware('can:viewAny,App\Models\Notification')->group(function() {
    Route::get('notifications/unseen', [NotificationController::class, 'unseenCount']);
    Route::delete('notifications', [NotificationController::class, 'deleteAllNotifications']);
    Route::post('notifications/read-all', [NotificationController::class, 'readAllNotifications']);
    Route::delete('notifications/read', [NotificationController::class, 'deleteReadNotifications']);
});

Route::get('users/{user}', [UserController::class, 'getUser'])->where('user', '[0-9a-zA-Z\-_]+');

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', [UserController::class, 'currentUser']);

    Route::resource('blocked-users', BlockedUserController::class)->except('show', 'update');
    Route::resource('blocked-tags', BlockedTagController::class)->except('show', 'update');
    Route::resource('followed-mods', FollowedModController::class)->except('show');
    Route::resource('followed-users', FollowedUserController::class)->except('show');
    Route::get('followed-users/mods', [FollowedUserController::class, 'mods']);
    Route::resource('followed-games', FollowedGameController::class)->except('show');
    Route::get('followed-games/mods', [FollowedGameController::class, 'mods']);
});

Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class)->only(['index', 'show']);

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout']);

/**
 * @hideFromAPIDocumentation
 */
Route::get('/auth/steam/redirect', function(Request $request) {
    return Socialite::driver('steam')->redirect();
});
Route::get('/auth/twitter/redirect', function(Request $request) {
    return Socialite::driver('twitter')->redirect();
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

/**
 * @hideFromAPIDocumentation
 */
Route::get('/auth/twitter/callback', function(Request $request) {
    $twitterUser = Socialite::driver('twitter')->user();

    /**
     * @var SocialLogin
     */
    $socialLogin = SocialLogin::where('social_id', 'twitter')->where('special_id', $twitterUser->id)->first();
    $user = null;
    if (isset($socialLogin)) {
        $user = $socialLogin->user;
    } else {
        //Create a user
        $user = User::create([
            'name' => $twitterUser->nickname,
            'avatar' => $twitterUser->avatar,
        ]);

        //Create a social login so the user can login with it later
        SocialLogin::create([
            'social_id' => 'twitter',
            'special_id' => $twitterUser->id,
            'user_id' => $user->id
        ]);
    }

    if (Auth::login($user, true)) {
        $request->session()->regenerate();
    }

    // return redirect('http://localhost:3000');
});