<?php

namespace App\Http\Resources;

use App\Models\Mod;
use App\Models\Report;
use App\Models\User;
use App\Services\APIService;
use Arr;
use Auth;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Models\Game;
use Illuminate\Http\Resources\MissingValue;
use JsonSerializable;

/**@mixin Game */
class GameResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        /** @var User */
        $user = $request->user();

        $moderateUsers = $user?->hasPermission('moderate-users', $this->resource);
        $manageMods = $user?->hasPermission('manage-mods', $this->resource);

        $isCurrent = APIService::currentGame()?->id === $this->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'disporder' => $this->disporder,
            'thumbnail' => $this->thumbnail,
            'banner' => $this->banner,
            'buttons' => $this->buttons,
            'last_date' => $this->last_date,
            'mod_count' => $this->mod_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'forum_id' => $this->forum_id,
            'default_mod_manager_id' => $this->default_mod_manager_id,

            'followed' => $this->whenLoaded('followed'),
            'ignored' => $this->whenLoaded('ignored'),

            'mods' => $this->whenLoaded('mods'),
            'tags' => $this->whenLoaded('tags'),
            'roles' => $this->whenLoaded('roles'),
            'categories' => $this->whenLoaded('categories'),
            'reports' => $this->whenLoaded('reports'),
            'reports' => $this->whenLoaded('reports'),
            'mod_managers' => $this->whenLoaded('modManagers'),

            'webhook_url' => $this->when($user?->hasPermission('manage-game', $this->resource), $this->webhook_url),
            'report_count' => $this->when($isCurrent && $moderateUsers, fn() => $this->reportCount),
            'waiting_count' => $this->when($isCurrent && $manageMods, fn() => $this->waitingCount),
            'user_data' => $this->when($isCurrent, fn() => $this->userData),
            'announcements' => $this->when($isCurrent, fn() => $this->announcements),
            'mods_count' => $this->whenCounted('viewableMods'),
            'mod_manager_ids' => $this->whenLoaded('modManagers', fn () => Arr::pluck($this->modManagers, 'id')),
        ];
    }
}
