<?php

namespace App\Http\Resources;

use App\Models\User;
use Arr;
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
            'role_ids' => $this->whenLoaded('roles', function() {
                $roleIds = Arr::pluck($this->roles, 'id');
                unset($roleIds[array_search(1, $roleIds)]); //Remove Members role
                return $roleIds;
            }),
            'tag' => $this->whenLoaded('roles', function() {
                foreach ($this->roles as $role) {
                    if ($role->tag) {
                        return $role->tag;
                    }
                }
            }),
            'color' => $this->whenLoaded('roles', function() {
                foreach ($this->roles as $role) {
                    if ($role->color) {
                        return $role->color;
                    }
                }
            })
        ]);
    }
}
