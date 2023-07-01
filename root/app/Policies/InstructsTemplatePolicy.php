<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\InstructsTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class InstructsTemplatePolicy
{
    use HandlesAuthorization;

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
     * @param InstructsTemplate $InstructsTemplate
     * @return Response|bool
     */
    public function view(?User $user, InstructsTemplate $InstructsTemplate)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Game $game=null)
    {
        return $user->hasPermission('manage-instructions', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param InstructsTemplate $InstructsTemplate
     * @return Response|bool
     */
    public function update(User $user, InstructsTemplate $instructsTemplate)
    {
        return $user->hasPermission('manage-instructions', $instructsTemplate->game);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param InstructsTemplate $InstructsTemplate
     * @return Response|bool
     */
    public function delete(User $user, InstructsTemplate $instructsTemplate)
    {
        return $user->hasPermission('manage-instructions', $instructsTemplate->game);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param InstructsTemplate $InstructsTemplate
     * @return Response|bool
     */
    public function restore(User $user, InstructsTemplate $InstructsTemplate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param InstructsTemplate $InstructsTemplate
     * @return Response|bool
     */
    public function forceDelete(User $user, InstructsTemplate $InstructsTemplate)
    {
        //
    }
}
