<?php

namespace App\Http\Controllers;

use App\Models\Mod;
use App\Models\ModMember;
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

        if ($mod->members()->where('user_id', $val['user_id'])->exists()) {
            abort(403, 'member already exists. If you wish to change their level, either use PATCH/PUT or DELETE first.');
        }

        $user = User::find($val['user_id']);

        $mod->members()->attach($user, ['level' => $val['level'], 'accepted' => false]);

        return $mod->members()->where('user_id', $val['user_id'])->first();
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
            'level' => 'integer|min:0|max:3'
        ]);

        if ($mod->user_id !== $ourUserId) {
            $ourMembership = $mod->members()->wherePivot('id', $ourUserId)->first();
            if ($ourMembership->level <= $val['level'] || $ourMembership->level <= $member->pivot->level) {
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
    public function destroy(Request $request, Mod $mod, User $member)
    {
        $ourUserId = $request->user()->id;
        $ourLevel = $mod->members->where('id', $ourUserId);

        //We should be able to delete ourselves from members!
        if ($ourUserId !== $member->id && $ourLevel <= $member->level) {
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

        $ourMembership = $mod->members()->wherePivot('user_id', $ourUserId)->wherePivot('accepted', false)->exists();

        if (!$ourMembership) {
            abort(401);
        }

        if ($val['accept']) {
            $mod->members()->updateExistingPivot($member, ['accepted' => true]);
        } else {
            $mod->members()->detach($member);
        }
    }
}
