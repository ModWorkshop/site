<?php

use App\Http\Controllers\BanController;
use App\Http\Controllers\BlockedTagController;
use App\Http\Controllers\BlockedUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FollowedGameController;
use App\Http\Controllers\FollowedModController;
use App\Http\Controllers\FollowedUserController;
use App\Http\Controllers\ForumCategoryController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameRoleController;
use App\Http\Controllers\ImageController;
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
use App\Http\Resources\UserResource;
use App\Models\Mod;
use App\Models\Report;
use App\Services\APIService;
use Illuminate\Http\Request;
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
 * @group Mods
 */
APIService::resource('files', FileController::class, 'mods');
APIService::resource('images', ImageController::class, 'mods');
Route::middleware('can:update,mod')->group(function() {
    Route::delete('mods/{mod}/files', [FileController::class, 'deleteAllFiles']);
    Route::delete('mods/{mod}/images', [ImageController::class, 'deleteAllImages']);
});

Route::middleware('can:super-update,mod')->group(function() {
    Route::patch('mods/{mod}/owner', [ModController::class, 'transferOwnership']);
    Route::patch('mods/{mod}/transfer-request/cancel', [ModController::class, 'cancelTransferRequest']);
});

Route::middleware('can:view,file')->get('files/{file}/download', [FileController::class, 'downloadFile']);

//General mods
APIService::resource('links', LinkController::class, 'mods');
Route::resource('mods.members', ModMemberController::class)->only(['store', 'destroy', 'update']);
Route::resource('mods.dependencies', ModDependencyController::class);
Route::patch('mods/{mod}/members/{member}/accept', [ModMemberController::class, 'accept']);
Route::patch('mods/{mod}/transfer-request/accept', [ModController::class, 'acceptTransferRequest']);
APIService::gameResource('mods', ModController::class);
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

APIService::resource('comments', ModCommentsController::class, 'mods');
Route::middleware('auth:sanctum')->group(function() {
    Route::middleware('can:like,mod')->post('mods/{mod}/toggle-liked', [ModController::class, 'toggleLike']);
    Route::get('mods/liked', [ModController::class, 'liked']);
});

//Games/categories/tags
APIService::gameResource('categories', CategoryController::class);
Route::resource('games', GameController::class);
Route::get('games/{game}/categories', [CategoryController::class, 'index']);
Route::get('games/{game}/users/{user}', [GameController::class, 'getGameUserData']);
Route::patch('games/{game}/users/{user}/roles', [GameController::class, 'setUserGameRoles']);
APIService::gameResource('tags', TagController::class, ['parentOptional' => true]);
Route::resource('games.instructs-templates', InstructsTemplateController::class);
Route::resource('instructs-templates.dependencies', InstructsTemplateDependencyController::class);
APIService::gameResource('roles', GameRoleController::class, ['shallow' => false])->parameters([
    'roles' => 'game-role'
]);

/**
 * @group Forums
 */
Route::resource('forums', ForumController::class)->only(['index', 'show', 'update']);
APIService::gameResource('forum-categories', ForumCategoryController::class, ['parentOptional' => true]);
APIService::resource('threads', ThreadController::class, 'forums');
APIService::resource('comments', ThreadCommentsController::class, 'threads');
Route::middleware('can:create,App\Models\Report')->post('threads/{thread}/reports', [ThreadController::class, 'report']);
Route::get('threads/{thread}/comments/{comment}/page', [ThreadCommentsController::class, 'page']);
Route::get('threads/{thread}/comments/{comment}/replies', [ThreadCommentsController::class, 'replies']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('threads/{thread}/comments/subscription', [ThreadCommentsController::class, 'subscribe']);
    Route::delete('threads/{thread}/comments/subscription', [ThreadCommentsController::class, 'unsubscribe']);
    Route::post('comments/{comment}/subscription', [ThreadCommentsController::class, 'subscribeComment']);
    Route::delete('comments/{comment}/subscription', [ThreadCommentsController::class, 'unsubscribeomment']);

    Route::post('mods/{mod}/comments/subscription', [ModCommentsController::class, 'subscribe']);
    Route::delete('mods/{mod}/comments/subscription', [ModCommentsController::class, 'unsubscribe']);
    Route::post('comments/{comment}/subscription', [ModCommentsController::class, 'subscribeComment']);
    Route::delete('comments/{comment}/subscription', [ModCommentsController::class, 'unsubscribeComment']);

    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
    Route::patch('comments/{comment}', [CommentController::class, 'update']);
    Route::get('comments/{comment}/page', [CommentController::class, 'page']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'replies']);
    Route::post('comments/{comment}/reports', [CommentController::class, 'report']);
});

/**
 * @group Users
 */
Route::resource('users', UserController::class)->except(['store', 'show']);
APIService::gameResource('bans', BanController::class, ['parentOptional' => true]);
APIService::gameResource('user-cases', UserCaseController::class, ['parentOptional' => true]);
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
APIService::gameResource('suspensions', SuspensionController::class, ['parentOptional' => true]);
APIService::gameResource('documents', DocumentController::class, ['parentOptional' => true]);
Route::get('documents/{document}', [DocumentController::class, 'getDocument']);
APIService::gameResource('reports', ReportController::class)->only(['index', 'update', 'destroy']);
Route::resource('permissions', PermissionController::class)->only(['index', 'show']);
Route::get('settings', [SettingsController::class, 'index']);
Route::middleware('can:update,App\Models\Setting')->patch('settings', [SettingsController::class, 'update']);

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

Route::get('site-data', function(Request $request) {
    $unseen = APIService::getUnseenNotifications();
    $announcements = APIService::getAnnouncements();
    $settings = APIService::getSettings();

    $data = [
        'unseen_notifications' => $unseen,
        'announcements' => $announcements,
        'settings' => $settings,
    ];

    if (Auth::hasUser()) {
        $user = Auth::user();
        if ($user->hasPermission('moderate-users')) {
            $data['reports_count'] = Report::whereArchived(false)->count();
            $data['waiting_count'] = Mod::whereApproved(false)->count();
        }

        $user = $request->user();
        $user->append('signable');
        $user->load('extra');

        $data['user'] = new UserResource($user);
    }

    return $data;
});

Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/resend', [UserController::class, 'resendEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::get('/check-reset-token', [LoginController::class, 'checkResetToken']);
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->middleware('guest')->name('password.update');