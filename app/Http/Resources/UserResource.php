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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unique_name' => $this->unique_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'avatar' => $this->avatar,
            'role_names' => $this->role_names,
            'permissions' => $this->permissions,
            'tag' => $this->tag,
            'email' => $this->when($user?->id === $this->id, $this->email),
            'role_ids' => $this->whenLoaded('roles', function() {
                $roleIds = Arr::pluck($this->roles, 'id');
                unset($roleIds[array_search(1, $roleIds)]); //Remove Members role
                return $roleIds;
            }),
            'custom_color' => $this->custom_color,
            'tag' => $this->whenLoaded('roles', function() {
                foreach ($this->roles as $role) {
                    if ($role->tag) {
                        return $role->tag;
                    }
                }
            }),
            'color' => $this->whenLoaded('roles', function() {
                if ($this->custom_color) {
                    return $this->custom_color;
                }
                foreach ($this->roles as $role) {
                    if ($role->color) {
                        return $role->color;
                    }
                }
            }, $this->custom_color),
            $this->mergeWhen($this->relationLoaded('extra'), function() {
                $extra = $this->extra;
                return [
                    'banner' => $extra->banner,
                    'bio' => $extra->bio,
                    'private_profile' => $extra->private_profile,
                    'custom_title' => $extra->custom_title,
                    'last_online' => $extra->last_online,
                    'donation_url' => $extra->donation_url
                ];
            }),
        ];
    }
}
