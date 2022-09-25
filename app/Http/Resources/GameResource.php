<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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

        return [...parent::toArray($request),
            $this->whenLoaded('followed', fn() => isset($this->followed)),
            'webhook_url' => $this->when($user?->hasPermission('manage-games'), $this->webhook_url)
        ];
    }
}
