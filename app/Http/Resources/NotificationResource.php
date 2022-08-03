<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Mod;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

const notificationObjectTypes = [
    User::class => 'user',
    Mod::class => 'mod',
    Thread::class => 'thread',
    Comment::class => 'comment'
];

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
            'notifiable_type' => notificationObjectTypes[$this->notifiable_type],
            'context_type' => isset($this->context_type) ? notificationObjectTypes[$this->context_type] : new MissingValue,
        ]);
    }
}
