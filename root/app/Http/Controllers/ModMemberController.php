<?php

namespace App\Http\Controllers;

use App\Models\Mod;
use App\Models\ModMember;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Log;

const MOD_MEMBER_RULES_OVER = [
    'owner' => ['maintainer', 'collaborator', 'contributor', 'viewer'],
    'maintainer' => ['collaborator', 'contributor', 'viewer'],
    'collaborator' => ['contributor', 'viewer'],
];

class ModMemberController extends Controller
{
    public function __construct() {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        $this->authorize('storeMember', $mod);
        $val = $request->validate([
            'user_id' => 'integer|required|min:1|exists:users,id',
            'level' => 'in:maintainer,collaborator,viewer,contributor|required'
        ]);

        $exists = $mod->getMemberLevel($val['user_id'], false);
        if (isset($exists)) {
            abort(403, 'member already exists. If you wish to change their level, either use PATCH/PUT or DELETE first.');
        }

        # Obviously avoid the owner giving it to themselves. May have weird bugs.
        $ourUserId = $this->userId();
        if ($mod->user_id === $val['user_id']) {
            abort(403, 'Cannot add the owner as a member!');
        }

        # Make sure we can add members with the given level
        if ($mod->user_id !== $ourUserId && !$this->user()->hasPermission('manage-mods', $mod->game)) {
            $ourLevel = $mod->getMemberLevel($ourUserId, false);
            if (!isset(MOD_MEMBER_RULES_OVER[$ourLevel]) || !in_array($val['level'], MOD_MEMBER_RULES_OVER[$ourLevel])) {
                abort(403, 'You cannot add members with this level!');
            }
        }

        $user = User::find($val['user_id']);

        if ($user->isBanned($mod->game_id)) {
            abort(405, 'Cannot add banned users to members.');
        }

        $mod->members()->attach($user, ['level' => $val['level'], 'accepted' => false]);
        $member = $mod->members()->where('user_id', $val['user_id'])->first();

        Notification::send(
            notifiable: $mod,
            user: $user,
            type: 'membership_request'
        );

        return [...$member->toArray(), 'level' => $member->pivot->level];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mod $mod, User $member)
    {
        $this->authorize('updateMember', [$mod, $member]);

        $ourUserId = $request->user()->id;
        $val = $request->validate([
            'level' => 'required|in:collaborator,maintainer,viewer,contributor'
        ]);

        if ($mod->user_id !== $ourUserId && !$this->user()->hasPermission('manage-mods', $mod->game)) {
            $ourLevel = $mod->getMemberLevel($ourUserId);
            $theirLevel = $mod->getMemberLevel($member->id, false);
            if (!isset(MOD_MEMBER_RULES_OVER[$ourLevel]) || !in_array($theirLevel, MOD_MEMBER_RULES_OVER[$ourLevel]) || !in_array($val['level'], MOD_MEMBER_RULES_OVER[$ourLevel])) {
                abort(403, "You don't have permission to set such a level.");
            }
        }

        $mod->members()->updateExistingPivot($member, $val);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Mod $mod, User $member)
    {
        $this->authorize('deleteMember', [$mod, $member]);

        $ourUserId = $request->user()->id;
        $memberLevel = $mod->getMemberLevel($member->id, false);

        //We should be able to delete ourselves from members!

        if ($ourUserId !== $member->id && !$this->user()->hasPermission('manage-mods', $mod->game)) {
            $ourLevel = $mod->getMemberLevel($ourUserId, false);

            if (!isset(MOD_MEMBER_RULES_OVER[$ourLevel]) || !in_array($memberLevel, MOD_MEMBER_RULES_OVER[$ourLevel])) {
                abort(403);
            }
        }

        $mod->members()->detach($member);
    }

    /**
     * Accepts incoming member request and make it active or delete it if rejected.
     *
     * @param Request $request
     * @param Mod $mod
     * @param User $member
     * @return void
     */
    public function accept(Request $request, Mod $mod)
    {
        $this->authorize('acceptMember', $mod);

        $val = $request->validate([
            'accept' => 'boolean|required'
        ]);

        //Of course, only accept an actual request.
        $member = $mod->members()->where('user_id', $this->userId())->firstOrFail();

        if ($val['accept']) {
            $mod->members()->updateExistingPivot($member, ['accepted' => true]);
        } else {
            $mod->members()->detach($member);
        }

        Notification::deleteRelated($member, 'membership_request');
    }
}
