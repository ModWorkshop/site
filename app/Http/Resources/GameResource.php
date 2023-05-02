<?php

namespace App\Http\Resources;

use App\Models\Mod;
use App\Models\Report;
use App\Models\User;
use App\Services\APIService;
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

        $isCurrent = APIService::currentGame() === $this->id;

        return [
            ...parent::toArray($request),
            $this->whenLoaded('followed', fn() => isset($this->followed)),
            'followed' => $this->whenLoaded('followed'),
            'webhook_url' => $this->when($user?->hasPermission('manage-game', $this->resource), $this->webhook_url),
            'report_count' => $this->when($isCurrent && $moderateUsers, fn() => $this->reportCount),
            'waiting_count' => $this->when($isCurrent && $manageMods, fn() => $this->waitingCount),
            'user_data' => $this->when($isCurrent, fn() => $this->userData),
            'announcements' => $this->when($isCurrent, fn() => $this->announcements),
        ];
    }
}
