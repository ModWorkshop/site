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
use App\Http\Controllers\IgnoredGameController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InstructsTemplateController;
use App\Http\Controllers\InstructsTemplateDependencyController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ModCommentsController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\ModDependencyController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\IgnoredModController;
use App\Http\Controllers\ModManagerController;
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
use App\Http\Controllers\TokenController;
use App\Http\Resources\UserResource;
use App\Models\Game;
use App\Models\IgnoredGame;
use App\Models\Mod;
use App\Models\Report;
use App\Models\TrackSession;
use App\Services\APIService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

## Files
APIService::resource('files', FileController::class, 'mods');
APIService::resource('images', ImageController::class, 'mods');

Route::middleware('can:view,file')->get('files/{file}/download', [FileController::class, 'downloadFile']);
Route::middleware('can:view,file')->get('files/{file}/version', [FileController::class, 'fileVersion']);
Route::post('files/{file}/register-download', [FileController::class, 'registerDownload']);
Route::middleware('can:view,mod')->get('mods/{mod}/download', [ModController::class, 'downloadPrimaryFile']);

// @group Mods
Route::middleware('can:view,mod')->group(function() {
    Route::get('mods/{mod}/version', [ModController::class, 'getVersion']);
    Route::get('mods/{mod}/files/latest', [FileController::class, 'getLatestFile']);
    Route::get('mods/{mod}/files/latest/version', [FileController::class, 'getLatestFileVersion']);
    Route::get('mods/{mod}/files/latest/download', [FileController::class, 'downloadLatestFileVersion']);
    Route::get('mods/{mod}/files/primary', [FileController::class, 'getPrimaryFile']);
    Route::get('mods/{mod}/files/primary/version', [FileController::class, 'getPrimaryFileVersion']);
    Route::get('mods/{mod}/files/{version}', [FileController::class, 'getFileByVersion']);
});

//General mods
APIService::resource('links', LinkController::class, 'mods');
Route::post('links/{link}/register-download', [LinkController::class, 'registerDownload']);
APIService::gameResource('mods', ModController::class);

Route::middleware('can:update,file')->post('files/{file}/begin-pending', [FileController::class, 'fileBeginUpload']);
Route::middleware('can:create,App\Models\File,mod')->post('mods/{mod}/files/begin-pending', [FileController::class, 'beginUpload']);
Route::middleware('can:complete,pendingFile')->post('pending-files/{pendingFile}/complete', [FileController::class, 'completePendingFileUpload']);

Route::middleware('can:update,mod')->group(function() {
    Route::delete('mods/{mod}/files', [FileController::class, 'deleteAllFiles']);
    Route::delete('mods/{mod}/images', [ImageController::class, 'deleteAllImages']);
});
Route::middleware('can:super-update,mod')->group(function() {
    Route::patch('mods/{mod}/owner', [ModController::class, 'transferOwnership']);
    Route::patch('mods/{mod}/owner/cancel', [ModController::class, 'cancelTransferRequest']);
});
Route::patch('mods/{mod}/members/accept', [ModMemberController::class, 'accept']);
Route::resource('mods.members', ModMemberController::class)->only(['store', 'destroy', 'update']);
Route::resource('mods.dependencies', ModDependencyController::class);
Route::patch('mods/{mod}/owner/accept', [ModController::class, 'acceptTransferRequest']);
Route::post('mods/{mod}/register-view', [ModController::class, 'registerView']);
Route::get('mods/waiting', [ModController::class, 'waiting']);
Route::get('games/{game}/mods/waiting', [ModController::class, 'waiting']);
Route::get('games/{game}/mods', [ModController::class, 'index']);
Route::get('games/{game}/popular-and-latest', [ModController::class, 'popularAndLatest']);
Route::middleware('auth:sanctum')->get('games/{game}/admin-data', [GameController::class, 'getAdminData']);
Route::get('popular-and-latest', [ModController::class, 'popularAndLatest']);

Route::get('mods/versions', [ModController::class, 'getVersions']);

Route::middleware('can:manage,mod')->group(function() {
    Route::post('mods/{mod}/suspensions', [ModController::class, 'suspend']);
    Route::patch('mods/{mod}/approved', [ModController::class, 'approve']);
});
Route::middleware('can:report,mod')->post('mods/{mod}/reports', [ModController::class, 'report']);

APIService::resource('comments', ModCommentsController::class, 'mods');
Route::middleware('auth:sanctum')->group(function() {
    Route::middleware('can:like,mod')->post('mods/{mod}/toggle-liked', [ModController::class, 'toggleLike']);
    Route::get('mods/liked', [ModController::class, 'liked']);
    Route::get('mods/followed', [ModController::class, 'followed']);
    Route::middleware('can:view,mod')->post('mods/{mod}/comments/subscription', [ModCommentsController::class, 'subscribe']);
    Route::delete('mods/{mod}/comments/subscription', [ModCommentsController::class, 'unsubscribe']);
});
APIService::gameResource('mod-managers', ModManagerController::class, ['parentOptional' => true]);

//Games/categories/tags
APIService::gameResource('categories', CategoryController::class);
Route::middleware('can:massUpdateMods,category')->patch('categories/{category}/mods', [CategoryController::class, 'updateMods']);
Route::resource('games', GameController::class);
Route::get('games/{game}/game-section-data', [GameController::class, 'gameSectionData']);
Route::get('games/{game}/categories', [CategoryController::class, 'index']);
Route::get('games/{game}/users/{user}', [GameController::class, 'getGameUser']);
Route::get('games/{game}/users', [UserController::class, 'index']);
Route::get('games/{game}/users/{user}/data', [GameController::class, 'getGameUserData']);
Route::get('games/{game}/data', [GameController::class, 'getGameData']);
Route::patch('games/{game}/users/{user}/roles', [GameController::class, 'setUserGameRoles']);
APIService::gameResource('tags', TagController::class, ['parentOptional' => true]);
APIService::gameResource('instructs-templates', InstructsTemplateController::class);
Route::resource('instructs-templates.dependencies', InstructsTemplateDependencyController::class);
APIService::gameResource('roles', GameRoleController::class, ['shallow' => false])->parameters([
    'roles' => 'game-role'
]);

/**
 * @group Forums
 */

Route::resource('forums', ForumController::class)->only(['index', 'show']);
APIService::gameResource('forum-categories', ForumCategoryController::class, ['parentOptional' => true]);
APIService::resource('threads', ThreadController::class, 'forums');
APIService::resource('comments', ThreadCommentsController::class, 'threads');
Route::resource('comments', CommentController::class)->only(['destroy', 'update', 'show']);

Route::middleware('can:report,thread')->post('threads/{thread}/reports', [ThreadController::class, 'report']);
Route::middleware('auth:sanctum')->group(function() {
    Route::middleware('can:view,thread')->post('threads/{thread}/comments/subscription', [ThreadCommentsController::class, 'subscribe']);
    Route::delete('threads/{thread}/comments/subscription', [ThreadCommentsController::class, 'unsubscribe']);
});

Route::middleware('auth:sanctum')->delete('comments/{comment}/subscription', [CommentController::class, 'unsubscribe']); //A user should be allowed to unsubscribe anytime
Route::middleware('can:view,comment')->group(function() {
    Route::middleware('auth:sanctum')->post('comments/{comment}/subscription', [CommentController::class, 'subscribe']);
    Route::get('comments/{comment}/page', [CommentController::class, 'page']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'replies']);
    Route::middleware('can:pin,comment')->patch('comments/{comment}/pinned', [CommentController::class, 'setPinned']);

    Route::middleware('can:report,comment')->post('comments/{comment}/reports', [CommentController::class, 'report']);
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

Route::middleware('can:viewDiscussions,user')->get('users/{user}/comments', [UserController::class, 'getComments']);
Route::middleware('can:viewDiscussions,user')->get('users/{user}/threads', [UserController::class, 'getThreads']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', [UserController::class, 'currentUser']);
    Route::patch('/user', [UserController::class, 'updateCurrent']);
    Route::middleware('throttle:1,1')->get('/user-data', [UserController::class, 'userData']);
    Route::patch('users/{user}/roles', [UserController::class, 'setUserRoles']);
    Route::middleware('can:manageMods,user')->delete('users/{user}/mods', [UserController::class, 'deleteMods']);
    Route::middleware('can:manageDiscussions,user')->delete('users/{user}/discussions', [UserController::class, 'deleteDiscussions']);
    Route::middleware('can:purge,user')->post('users/{user}/purge', [UserController::class, 'purgeUser']);
    Route::resource('blocked-users', BlockedUserController::class)->except('show', 'update');
    Route::resource('blocked-tags', BlockedTagController::class)->except('show', 'update');
    Route::resource('followed-mods', FollowedModController::class)->except('show', 'update');
    Route::resource('followed-users', FollowedUserController::class)->except('show', 'update');
    Route::get('followed-users/mods', [FollowedUserController::class, 'mods']);
    Route::resource('followed-games', FollowedGameController::class)->except('show', 'update');
    Route::resource('ignored-games', IgnoredGameController::class)->except('show', 'update');
    Route::resource('ignored-mods', IgnoredModController::class)->except('show', 'update');
    Route::get('followed-games/mods', [FollowedGameController::class, 'mods']);
});

Route::resource('supporters', SupporterController::class);
Route::post('supporters/tebex/webhook', [SupporterController::class, 'tebexWebhook']);
Route::middleware('auth:sanctum')->group(function() {
    Route::get('supporters/nitro-check', [SupporterController::class, 'nitroCheck']);
    Route::middleware('throttle:10,1')->get('supporters/tebex/baskets', [SupporterController::class, 'tebexCreateBasket']);
});

Route::middleware('can:report,user')->post('users/{user}/reports', [UserController::class, 'report']);
Route::resource('roles', RoleController::class);
APIService::gameResource('suspensions', SuspensionController::class, ['parentOptional' => true, 'gameOnly' => ['index']]);
APIService::gameResource('documents', DocumentController::class, ['parentOptional' => true]);
Route::get('documents/{document}', [DocumentController::class, 'getDocument']);
APIService::gameResource('reports', ReportController::class)->only(['index', 'update', 'destroy']);
APIService::gameResource('audit-logs', AuditLogController::class)->only(['index', 'show', 'destroy']);
Route::resource('permissions', PermissionController::class)->only(['index', 'show']);
Route::get('settings', [SettingsController::class, 'index']);
Route::middleware('can:update,App\Models\Setting')->patch('settings', [SettingsController::class, 'update']);

/**
 * @hideFromAPIDocumentation
 */
Route::middleware('auth:sanctum')->group(function() {
    Route::get('social-logins', [SocialLoginController::class, 'index']);
    Route::delete('/social-logins/{provider}', [SocialLoginController::class, 'destroy']);
    Route::post('social-logins/{provider}/link-callback', [SocialLoginController::class, 'linkAccountCallback']);
});
Route::get('social-logins/{provider}/link-redirect', [SocialLoginController::class, 'linkAccountRedirect']);
Route::get('social-logins/{provider}/login-redirect', [LoginController::class, 'socialiteRedirect']);
Route::post('social-logins/{provider}/login-callback', [LoginController::class, 'socialiteLogin']);

# Login stuff
Route::middleware('throttle:10,1')->group(function() {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
    Route::post('/reset-password', [LoginController::class, 'resetPassword'])->middleware('guest')->name('password.update');
});
Route::post('/email/resend', [UserController::class, 'resendEmail'])->middleware(['auth', 'throttle:1,1'])->name('verification.send');
Route::post('/email/cancel-pending', [UserController::class, 'cancelPendingEmail'])->middleware(['auth', 'throttle:1,1']);

Route::get('site-data', function(Request $request) {
    $unseen = APIService::getUnseenNotifications();
    $announcements = APIService::getAnnouncements();
    $settings = APIService::getSettings();
    $MinAgo = Carbon::now()->subMinutes(15);
    $users = TrackSession::whereNotNull('user_id')->where('updated_at', '>', $MinAgo)->count();
    $guests = TrackSession::whereNull('user_id')->where('updated_at', '>', $MinAgo)->count();

    $games = Game::OrderByRaw('last_date DESC nulls last')
        ->withCount('viewableMods')
        ->whereNotIn('id', fn($q) => $q->select('game_id')->from('ignored_games')->where('user_id', Auth::id()))
        ->get(10);

    $data = [
        'unseen_notifications' => $unseen,
        'announcements' => $announcements,
        'settings' => $settings,
        'games' => $games,
        'activity' => [
            'users' => $users,
            'guests' => $guests
        ]
    ];

    if (Auth::hasUser()) {
        $user = Auth::user();
        if ($user->hasPermission('moderate-users')) {
            $data['report_count'] = Report::whereArchived(false)->count();
            $data['waiting_count'] = Mod::whereApproved(null)->count();
        }

        $user = $request->user();
        $user->append('signable');
        $user->load('extra');

        $data['user'] = new UserResource($user);
    }

    return $data;
});

Route::get('admin-data', fn() => APIService::adminData());

Route::get('v2API', function(Request $request) {
    $val = $request->validate([
        'command' => 'string',
        'did' => 'integer',
        'fid' => 'integer',
        'vid' => 'string',
    ]);

    $mod = null;
    if (isset($val['did'])) {
        $mod = Mod::where('id', $val['did'])->firstOrFail();
    }

    switch($val['command']) {
        case 'CompareVersion':
            if (!isset($mod)) {
                return;
            }

            return ($val['vid'] ?? null) == $mod->version ? 'true ' : $mod->version . ' ';
        case 'Version':
            if (!isset($mod)) {
                return;
            }

            return $mod->version;
        case 'AssocFiles':
            if (!isset($mod)) {
                return;
            }

            $response = '';
            foreach($mod->files as $file) {
                $response .= '"'.$file->id.'"'.str_replace('"',"''",$file->name).'",';
            }
            return substr($response, 0, -1);
        case 'DownloadFile':
            if (!isset($val['fid'])) {
                return;
            }

            return redirect("/files/{$val['fid']}/download");
    }
    echo $val['command'];
});

// Route::middleware('has_permission:create-api-tokens')->resource('tokens', TokenController::class);
