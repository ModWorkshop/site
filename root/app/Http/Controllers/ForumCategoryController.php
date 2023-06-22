<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\Game;
use App\Models\GameRole;
use App\Models\Role;
use App\Services\APIService;
use App\Services\Utils;
use Arr;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumCategoryController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(ForumCategory::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'forum_id' => 'integer|min:1|exists:forums,id',
            'game_id' => 'integer|min:1|nullable|exists:games,id'
        ]);

        
        return JsonResource::collection(ForumCategory::queryGet($val, function($query, array $val) {
            if (isset($val['forum_id'])) {
                $query->where('forum_id', $val['forum_id']);
            }
            if (isset($val['game_id'])) {
                $query->whereRelation('forum', fn($q) => $q->where('game_id', $val['game_id']));
            }

            Utils::forumCategoriesFilter($query);
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Game $game=null)
    {
        return $this->update($request, null, $game);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ForumCategory $forumCategory)
    {
        return $forumCategory;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ForumCategory $forumCategory=null, Game $game=null)
    {
        $val = $request->validate([
            'name' => 'string|nullable|min:3|max:150',
            'emoji' => 'string|nullable|max:6',
            'desc' => 'string|nullable|max:1000|',
            'is_private' => 'boolean',
            'banned_can_post' => 'boolean',
            'private_threads' => 'boolean',
            'role_policies' => 'array',
            'game_role_policies' => 'array',
        ]);

        $rolesArr = Arr::pull($val, 'role_policies');
        $gameRolesArr = Arr::pull($val, 'game_role_policies');
 
        APIService::nullToEmptyStr($val, 'desc', 'emoji');

        if (isset($forumCategory)) {
            $forumCategory->update($val);
        } else {
            $val['forum_id'] = $game?->forum_id ?? 1;
            $forumCategory = ForumCategory::create($val);
        }

        $syncRoles = [];
        $syncGameRoles = [];

        if (isset($rolesArr)) {
            $roles = Role::whereIn('id', array_keys($rolesArr))->where('is_vanity', false)->get();

            if (count($roles) !== count($rolesArr)) {
                abort(400, 'roles is invalid');
            }

            foreach ($roles as $role) {
                $perms = $rolesArr[$role->id];
                $canView = Arr::get($perms, 'can_view');
                $canPost = Arr::get($perms, 'can_post');

                if (is_bool($canView) && is_bool($canPost)) {
                    $syncRoles[$role->id] = ['can_view' => $canView, 'can_post' => $canPost];
                } else {
                    abort(400, 'roles is invallid');
                }
            }
        }

        if (isset($gameRolesArr)) {
            $gameRoles = GameRole::whereIn('id', array_keys($gameRolesArr))->where('is_vanity', false)->get();

            if (count($gameRoles) !== count($gameRolesArr)) {
                abort(400, 'game_roles is invalid');
            }

            foreach ($gameRoles as $role) {
                $perms = $gameRolesArr[$role->id];

                $canView = Arr::get($perms, 'can_view');
                $canPost = Arr::get($perms, 'can_post');

                if (is_bool($canView) && is_bool($canPost)) {
                    $syncGameRoles[$role->id] = ['can_view' => $canView, 'can_post' => $canPost];
                } else {
                    abort(400, 'game_roles is invallid');
                }
            }
        }

        if (isset($rolesArr)) {
            $forumCategory->roles()->sync($syncRoles);
            Role::flushQueryCache();
            $forumCategory->load('roles');
        }
        if (isset($gameRolesArr)) {
            $forumCategory->gameRoles()->sync($syncGameRoles);
            GameRole::flushQueryCache();
            $forumCategory->load('gameRoles');
        }

        return $forumCategory;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ForumCategory $forumCategory)
    {
        $forumCategory->delete();
    }
}
