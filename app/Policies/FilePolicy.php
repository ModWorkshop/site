<?php

namespace App\Policies;

use App\Models\File;
use App\Models\Mod;
use App\Models\PendingFile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FilePolicy
{
    use HandlesAuthorization;
    use AuthorizesRequests;

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
     * @param File $file
     * @return Response|bool
     */
    public function view(?User $user, File $file)
    {
        return $this->authorize('view', $file->mod);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Mod $mod)
    {
        return $this->authorize('update', $mod);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param File $file
     * @return Response|bool
     */
    public function update(User $user, File $file)
    {
        return $this->authorize('update', $file->mod);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param File $file
     * @return Response|bool
     */
    public function delete(User $user, File $file)
    {
        return $this->authorize('update', $file->mod);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param File $file
     * @return Response|bool
     */
    public function restore(User $user, File $file)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param File $file
     * @return Response|bool
     */
    public function forceDelete(User $user, File $file)
    {
        //
    }
}
