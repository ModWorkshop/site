<?php

namespace App\Policies;

use App\Models\File;
use App\Models\Image;
use App\Models\Mod;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ImagePolicy
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
    public function view(?User $user, Image $image)
    {
        return $this->authorize('view', $image->mod);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Mod $mod)
    {
        if ($mod->images()->count() >= 20) {
            return false;
        }

        return $this->authorize('update', $mod);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param File $file
     * @return Response|bool
     */
    public function update(User $user, Image $image)
    {
        return $this->authorize('update', $image->mod);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param File $file
     * @return Response|bool
     */
    public function delete(User $user, Image $image)
    {
        return $this->authorize('update', $image->mod);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param File $file
     * @return Response|bool
     */
    public function restore(User $user, Image $image)
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
    public function forceDelete(User $user, Image $image)
    {
        //
    }
}
