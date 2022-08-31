<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Mod;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'notifiable' => $this->notifiable,
            'context' => $this->context,
        ]);
    }
}
