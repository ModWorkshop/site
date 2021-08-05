<?php

namespace App\Policies;

use App\Models\Mod;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    public function update(User $user, Mod $mod) {
        return $user->id === $mod->submitter_uid;
    }
}
