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
            'email_verified_at' => $this->when($isMe, $this->email_verified_at),
            'activated' => $this->when($isMe, $this->activated),
            'followed' => $this->whenLoaded('followed'),
            'role_ids' => $this->whenLoaded('roles', fn() => array_filter(Arr::pluck($this->roles, 'id'), fn($id) => $id !== 1)),
            'game_role_ids' => $this->when(isset(User::$currentGameId), fn() => Arr::pluck($this->gameRoles, 'id')),
            'last_online' => $this->last_online,
            'blocked_by_me' => $this->when($notMeNotGuest, fn() => $this->blockedByMe),
            'blocked_me' => $this->when($notMeNotGuest, fn() => $this->blockedMe),
            'custom_color' => $this->custom_color,
            'highest_role_order' => $this->highestRoleOrder,
            'tag' => $this->tag,
            'banner' => $this->banner,
            'bio' => $this->bio,
            'invisible' => $this->invisible,
            'private_profile' => $this->private_profile,
            'custom_title' => $this->custom_title,
            'donation_url' => $this->donation_url,
            'invisible' => $this->invisible,
            'show_tag' => $this->show_tag,
            'supporter' => $this->activeSupporter,
            'signable' => $this->when($this->hasAppended('signable'), fn() => $this->signable),
            'extra' => $this->whenLoaded('extra')
        ];
    }
}
