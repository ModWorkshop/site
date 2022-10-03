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
        $notMeNotGuest = isset($user) && !$isMe;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'ban' => $this->last_ban,
            'game_ban' => $this->last_game_ban,
            'unique_name' => $this->unique_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'color' => $this->color,
            'avatar' => $this->avatar,
            'role_names' => Arr::pluck($this->roleList, 'name'),
            'permissions' => $this->permissionList,
            'tag' => $this->tag,
            'email' => $this->when($isMe, $this->email),
            'followed' => $this->whenLoaded('followed'),
            'role_ids' => $this->whenLoaded('roles', fn() => array_filter(Arr::pluck($this->roles, 'id'), fn($id) => $id !== 1)),
            'last_online' => $this->last_online,
            'blocked_by_me' => $this->when($notMeNotGuest, fn() => $this->blockedByMe),
            'blocked_me' => $this->when($notMeNotGuest, fn() => $this->blockedMe),
            'custom_color' => $this->custom_color,
            'highest_role_order' => $this->highestRoleOrder,
            'tag' => $this->whenLoaded('roles', function() {
                foreach ($this->roles as $role) {
                    if ($role->tag) {
                        return $role->tag;
                    }
                }
            }),
            $this->mergeWhen($this->relationLoaded('extra'), function() {
                $extra = $this->extra;
                return [
                    'banner' => $extra->banner,
                    'bio' => $extra->bio,
                    'private_profile' => $extra->private_profile,
                    'custom_title' => $extra->custom_title,
                    'donation_url' => $extra->donation_url
                ];
            }),
        ];
    }
}
