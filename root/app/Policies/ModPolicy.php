<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Log;

class ModPolicy
{
    use HandlesAuthorization, AuthorizesRequests;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Mod $mod
     * @return Response|bool
     */
    public function view(?User $user, Mod $mod)
    {
        if (!isset($user) || !$this->update($user, $mod)) {
            if ($mod->suspended) {
                return Response::deny('suspended');
            }

            if ($mod->approved === null) {
                return Response::deny('unapproved');
            }

            if ($mod->approved === false) {
                return Response::deny('rejected');
            }
        }

        switch ($mod->visibility) {
            case Visibility::unlisted:
            case Visibility::public:
                return true;
            case Visibility::private:
                if (!isset($user)) {
                    return false;
                }
                return $mod->members()->where('user_id', $user->id)->exists()
                    || $mod->transferRequest()->where('user_id', $user->id)->exists()
                    || $this->update($user, $mod);
        }
        return true;
    }

    public function like(User $user, Mod $mod)
    {
        return $user->id !== $mod->user_id && $this->view($user, $mod) && $user->hasPermission('like-mods', $mod->game, true);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Game $game)
    {
        return $user->hasPermission('create-mods', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Mod $mod
     * @return Response|bool
     */
    public function update(User $user, Mod $mod)
    {
        $game = $mod->game;

        if ($user->hasPermission('manage-mods', $game)) {
            return true;
        }

        if (!$user->hasPermission('create-mods', $game)) {
            return false;
        }

        if ($user->id === $mod->user_id) {
            return true;
        }

        $ourLevel = $mod->getMemberLevel($user->id);
        return ($ourLevel === 'maintainer' || $ourLevel === 'collaborator');
    }

    /**
     * Essentially the highest permission for a mod; owner or moderator.
     *
     * @param User $user
     * @param Mod $mod
     * @return void
     */
    public function superUpdate(User $user, Mod $mod)
    {
        return $user->id === $mod->user_id || $user->hasPermission('manage-mods', $mod->game);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Mod $mod
     * @return Response|bool
     */
    public function delete(User $user, Mod $mod)
    {
        $game = $mod->game;
        if ($user->hasPermission('manage-mods', $game)) {
            return true;
        }

        //Users should be able to delete their own mods unconditionally.
        if ($user->id === $mod->user_id) {
            return true;
        }

        //Maintainers shouldn't be able to do anything if they can't create mods.
        if (!$user->hasPermission('create-mods', $game)) {
            return false;
        }

        return $mod->getMemberLevel($user->id) === 'maintainer';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Mod $mod
     * @return Response|bool
     */
    public function restore(User $user, Mod $mod)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Mod $mod
     * @return Response|bool
     */
    public function forceDelete(User $user, Mod $mod)
    {
        //
    }

    public function createComment(User $user, Mod $mod)
    {
        if (!$user->hasPermission('create-discussions', $mod->game) || $mod->user->blockedMe) {
            return false;
        }

        if ($mod->comments_disabled) {
            return $this->update($user, $mod);
        } else {
            return $this->view($user, $mod);
        }
    }

    public function manage(User $user, Mod $mod, Game $game=null)
    {
        return $user->hasPermission('manage-mods', $mod->game);
    }

    public function manageAny(User $user, Game $game=null)
    {
        return $user->hasPermission('manage-mods', $game);
    }

    public function report(User $user, Mod $mod)
    {
        return !$mod->suspended && $user->hasPermission('create-reports', $mod->game);
    }

    public function storeMember(User $user, Mod $mod)
    {
        return $this->authorize('update', $mod);
    }

    public function updateMember(User $user, Mod $mod, User $member)
    {
        return $user->id === $member->user_id || $this->authorize('storeMember', $mod);
    }

    public function deleteMember(User $user, Mod $mod, User $member)
    {
        return $this->authorize('updateMember', [$mod, $member]);
    }

    public function acceptMember(User $user, Mod $mod)
    {
        return $mod->members()->where('user_id', $user->id)->exists();
    }
}
