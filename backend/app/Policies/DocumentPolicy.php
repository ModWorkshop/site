<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
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
     * @param Document $document
     * @return Response|bool
     */
    public function view(?User $user, Document $document)
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
        return $user->hasPermission('manage-documents', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Document $document
     * @return Response|bool
     */
    public function update(User $user, Document $document)
    {
        return $user->hasPermission('manage-documents', $document->game);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Document $document
     * @return Response|bool
     */
    public function delete(User $user, Document $document)
    {
        return $user->hasPermission('manage-documents', $document->game);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Document $document
     * @return Response|bool
     */
    public function restore(User $user, Document $document)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Document $document
     * @return Response|bool
     */
    public function forceDelete(User $user, Document $document)
    {
        //
    }
}
