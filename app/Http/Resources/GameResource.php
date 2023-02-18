<?php

namespace App\Http\Resources;

use App\Models\Mod;
use App\Models\Report;
use App\Models\User;
use Arr;
use Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var User */
        $user = $request->user();

        $moderateUsers = $user?->hasPermission('moderate-users', $this->resource);
        $manageMods = $user?->hasPermission('manage-mods', $this->resource);

        return [
            ...parent::toArray($request),
            $this->whenLoaded('followed', fn() => isset($this->followed)),
            'followed' => $this->whenLoaded('followed'),
            'webhook_url' => $this->when($user?->hasPermission('manage-game', $this->resource), $this->webhook_url),
            'reports_count' => $this->when($moderateUsers, fn() => $this->reports()->count()),
            'waiting_count' => $this->when($manageMods, fn() => $this->mods()->where('approved', false)->count()),
        ];
    }
}
