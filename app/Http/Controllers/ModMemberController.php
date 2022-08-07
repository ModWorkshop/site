<?php

namespace App\Http\Controllers;

use App\Models\Mod;
use App\Models\ModMember;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class ModMemberController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'user_id' => 'integer|min:1|exists:users,id',
            'level' => 'integer|min:0|max:4'
        ]);


        $exists = $mod->getMemberLevel($val['user_id'], false);

        if (isset($exists)) {
            abort(403, 'member already exists. If you wish to change their level, either use PATCH/PUT or DELETE first.');
        }

        $user = User::find($val['user_id']);
        
        $mod->members()->attach($user, ['level' => $val['level'], 'accepted' => false]);
        $member = $mod->members()->where('user_id', $val['user_id'])->first();

        Notification::send(
            notifiable: $mod,
            user: $user,
            type: 'membership_request'
        );

        return $member;
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
        $ourUserId = $request->user()->id;
        $val = $request->validate([
            'level' => 'integer|required|min:0|max:3'
        ]);

        if ($mod->user_id !== $ourUserId) {
            $ourLevel = $mod->getMemberLevel($ourUserId);
            if (isset($ourLevel) && ($ourLevel <= $val['level'] || $ourLevel <= $member->pivot->level)) {
                abort(401);
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
    public function destroy(Request $request, Mod $mod, int $member)
    {
        $ourUserId = $request->user()->id;
        $ourLevel = $mod->getMemberLevel($ourUserId, false);
        $memberLevel = $mod->getMemberLevel($member, false);

        //We should be able to delete ourselves from members!
        if (!isset($ourLevel) || ($ourUserId !== $member && $ourLevel >= $memberLevel)) {
            abort(401);
        }

        $mod->members()->detach($member);
    }

    /**
     * Accepts incoming member request and make it active or deletes it if rejected.
     *
     * @param Request $request
     * @param Mod $mod
     * @param User $member
     * @return void
     */
    public function accept(Request $request, Mod $mod, User $member)
    {
        $ourUserId = $request->user()->id;
        $val = $request->validate([
            'accept' => 'boolean|required'
        ]);

        $ourLevel = $mod->getMemberLevel($ourUserId, false);

        if (!isset($ourLevel)) {
            abort(401);
        }

        if ($val['accept']) {
            $mod->members()->updateExistingPivot($member, ['accepted' => true]);
        } else {
            $mod->members()->detach($member);
        }

        Notification::deleteRelated($member, 'membership_request');
    }
}
