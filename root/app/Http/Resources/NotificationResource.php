<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Mod;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use JsonSerializable;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'notifiable' => $this->notifiable,
            'context' => $this->context,
            'fromUser' => $this->fromUser
        ]);
    }
}
