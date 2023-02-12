<?php

use App\Http\Controllers\BanController;
use App\Http\Controllers\BlockedTagController;
use App\Http\Controllers\BlockedUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FollowedGameController;
use App\Http\Controllers\FollowedModController;
use App\Http\Controllers\FollowedUserController;
use App\Http\Controllers\ForumCategoryController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameRoleController;
use App\Http\Controllers\InstructsTemplateController;
use App\Http\Controllers\InstructsTemplateDependencyController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModCommentsController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\ModDependencyController;
use App\Http\Controllers\ModMemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\SupporterController;
use App\Http\Controllers\SuspensionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ThreadCommentsController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserCaseController;
use App\Http\Controllers\UserController;
use App\Models\Mod;
use App\Models\Report;
use App\Services\APIService;
use Illuminate\Support\Facades\Route;

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
 * Registers a game resource with also a direct resource link.
 * store method still requires a game so that's not available in the global one.
 */
function resource(string $resource, string $class, string $parent, array $config=[]) {
    $reg = Route::resource("{$parent}.{$resource}", $class);
    Route::resource($resource, $class)->only($config['selfOnly'] ?? ['index']);
    if ($config['shallow'] ?? true) {
        $reg->shallow();
    }
    $reg->except(['create', 'edit', ...($config['except'] ?? [])]);

    return $reg;
}
function gameResource(string $resource, string $class, array $config=[]) {
    return resource($resource, $class, 'games', $config);
}

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
Route::resource('mods.dependencies', ModDependencyController::class);
Route::patch('mods/{mod}/members/{member}/accept', [ModMemberController::class, 'accept']);
Route::patch('mods/{mod}/transfer-request/accept', [ModController::class, 'acceptTransferRequest']);
gameResource('mods', ModController::class);
Route::get('mods/followed', [ModController::class, 'followed']);
Route::post('mods/{mod}/register-view', [ModController::class, 'registerView']);
Route::post('mods/{mod}/register-download', [ModController::class, 'registerDownload']);
Route::get('mods/waiting', [ModController::class, 'waiting']);
Route::get('games/{game}/mods/waiting', [ModController::class, 'waiting']);
Route::middleware('can:manage,mod')->group(function() {
    Route::patch('mods/{mod}/suspended', [ModController::class, 'suspend']);
    Route::patch('mods/{mod}/approved', [ModController::class, 'approve']);
});
Route::middleware('can:report,mod')->post('mods/{mod}/reports', [ModController::class, 'report']);

Route::resource('mods.comments', ModCommentsController::class);
Route::middleware('can:report,mod')->post('mods/{mod}/comments/{comment}/reports', [ModCommentsController::class, 'report']);
Route::get('mods/{mod}/comments/{comment}/page', [ModCommentsController::class, 'page']);
Route::get('mods/{mod}/comments/{comment}/replies', [ModCommentsController::class, 'replies']);

Route::middleware('auth:sanctum')->group(function() {
    Route::middleware('can:like,mod')->post('mods/{mod}/toggle-liked', [ModController::class, 'toggleLike']);
    Route::get('mods/liked', [ModController::class, 'liked']);

    Route::post('mods/{mod}/comments/subscription', [ModCommentsController::class, 'subscribe']);
    Route::delete('mods/{mod}/comments/subscription', [ModCommentsController::class, 'unsubscribe']);
    Route::post('mods/{mod}/comments/{comment}/subscription', [ModCommentsController::class, 'subscribeComment']);
    Route::delete('mods/{mod}/comments/{comment}/subscription', [ModCommentsController::class, 'unsubscribeComment']);
});

//Games/categories/tags
gameResource('categories', CategoryController::class);
Route::resource('games', GameController::class);
Route::get('games/{game}/categories', [CategoryController::class, 'index']);
Route::get('games/{game}/users/{user}', [GameController::class, 'getGameUserData']);
Route::patch('games/{game}/users/{user}/roles', [GameController::class, 'setUserGameRoles']);
gameResource('tags', TagController::class);
Route::resource('games.instructs-templates', InstructsTemplateController::class);
Route::resource('instructs-templates.dependencies', InstructsTemplateDependencyController::class);
gameResource('roles', GameRoleController::class, ['shallow' => false])->parameters([
    'roles' => 'game-role'
]);

/**
 * @group Forums
 */
Route::resource('forums', ForumController::class)->only(['index', 'show', 'update']);
gameResource('forum-categories', ForumCategoryController::class);
resource('threads', ThreadController::class, 'forums');
Route::resource('threads.comments', ThreadCommentsController::class);
Route::middleware('can:create,App\Models\Report')->post('threads/{thread}/reports', [ThreadController::class, 'report']);
Route::get('threads/{thread}/comments/{comment}/page', [ThreadCommentsController::class, 'page']);
Route::get('threads/{thread}/comments/{comment}/replies', [ThreadCommentsController::class, 'replies']);
Route::middleware('can:create,App\Models\Report')->post('threads/{thread}/comments/{comment}/reports', [ThreadCommentsController::class, 'report']);
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
gameResource('bans', BanController::class, ['selfOnly' => ['index', 'store']]);
Route::middleware('can:report,mod')->post('mods/{mod}/comments/{comment}/reports', [ModController::class, 'report']);
gameResource('user-cases', UserCaseController::class, ['selfOnly' => ['index', 'store']]);
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
    Route::middleware('throttle:1,1')->get('/user-data', [UserController::class, 'userData']);
    Route::patch('users/{user}/roles', [UserController::class, 'setUserRoles']);
    Route::delete('users/{user}/mods', [UserController::class, 'deleteMods']);
    Route::delete('users/{user}/discussions', [UserController::class, 'deleteDiscussions']);

    Route::resource('blocked-users', BlockedUserController::class)->except('show', 'update');
    Route::resource('blocked-tags', BlockedTagController::class)->except('show', 'update');
    Route::resource('followed-mods', FollowedModController::class)->except('show');
    Route::resource('followed-users', FollowedUserController::class)->except('show');
    Route::get('followed-users/mods', [FollowedUserController::class, 'mods']);
    Route::resource('followed-games', FollowedGameController::class)->except('show');
    Route::get('followed-games/mods', [FollowedGameController::class, 'mods']);
});

Route::resource('supporters', SupporterController::class);

Route::middleware('can:create,App\Models\Report')->post('users/{user}/reports', [UserController::class, 'report']);
Route::resource('roles', RoleController::class);
gameResource('suspensions', SuspensionController::class);
gameResource('documents', DocumentController::class, ['show']);
Route::get('documents/{document}', [DocumentController::class, 'getDocument']);
gameResource('reports', ReportController::class)->only(['index', 'update', 'destroy']);
Route::resource('permissions', PermissionController::class)->only(['index', 'show']);
Route::get('settings', [SettingsController::class, 'index']);
Route::middleware('auth:sanctum')->patch('settings', [SettingsController::class, 'update']);

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [LoginController::class, 'register']);
Route::post('logout', [LoginController::class, 'logout']);

/**
 * @hideFromAPIDocumentation
 */
Route::middleware('auth:sanctum')->group(function() {
    Route::get('social-logins', [SocialLoginController::class, 'index']);
    Route::delete('/social-logins/{provider}', [SocialLoginController::class, 'destroy']);
    Route::post('social-logins/{provider}/link-callback', [SocialLoginController::class, 'linkAccountCallback']);
});
Route::get('social-logins/{provider}/link-redirect', [SocialLoginController::class, 'linkAccountRedirect']);
Route::get('social-logins/{provider}/login-redirect', [LoginController::class, 'SocialiteRedirect']);
Route::post('social-logins/{provider}/login-callback', [LoginController::class, 'SocialiteLogin']);

Route::get('site-data', function() {
    $unseen = APIService::getUnseenNotifications();
    $announcements = APIService::getAnnouncements();
    $settings = APIService::getSettings();

    return [
        'unseen_notifications' => $unseen,
        'announcements' => $announcements,
        'settings' => $settings,
    ];
});

Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/resend', [UserController::class, 'resendEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::get('/check-reset-token', [LoginController::class, 'checkResetToken']);
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->middleware('guest')->name('password.update');