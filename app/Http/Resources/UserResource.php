<?php

namespace App\Http\Resources;

use App\Models\User;
use Arr;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Request;

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
        /** @var User */
        $user = $request->user();

        $isMe = $user?->id === $this->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'ban' => $this->last_ban,
            'unique_name' => $this->unique_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'color' => $this->color,
            'avatar' => $this->avatar,
            'role_names' => $this->role_names,
            'permissions' => $this->permissions,
            'tag' => $this->tag,
            'email' => $this->when($isMe, $this->email),
            'followed' => $this->whenLoaded('followed'),
            'role_ids' => $this->whenLoaded('roles', function() {
                $roleIds = Arr::pluck($this->roles, 'id');
                unset($roleIds[array_search(1, $roleIds)]); //Remove Members role
                return $roleIds;
            }),
            'blocked_by_me' => $this->when(!$isMe, fn() => $this->blockedByMe),
            'blocked_me' => $this->when(!$isMe, fn() => $this->blockedMe),
            'custom_color' => $this->custom_color,
            'tag' => $this->whenLoaded('roles', function() {
                foreach ($this->roles as $role) {
                    if ($role->tag) {
                        return $role->tag;
                    }
                }
            }),
            $this->whenLoaded('extra', function() {
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
