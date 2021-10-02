<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $request->user();
        return array_merge(parent::toArray($request), [
            'email' => $this->when($user?->id === $this->id, $this->email),
            'color' => $this->whenLoaded('roles', function() {
                foreach ($this->roles as $role) {
                    if ($role->color) {
                        return $role->color;
                    }
                    return User::$membersRole->color;
                }  
            })
        ]);
    }
}
