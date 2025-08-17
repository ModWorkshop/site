<?php

namespace App\Http\Resources;

use Arr;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use JsonSerializable;

class ThreadResource extends BaseResource
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
            'forum' => $this->whenLoaded('forum', fn() => new ForumResource($this->forum)),
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'game' => $this->whenLoaded('game', fn() => new GameResource($this->game)),
            'answer_comment' => $this->whenLoaded('answerComment', fn() => new CommentResource($this->answerComment)),
            'tag_ids' => $this->whenLoaded('tags', fn () => Arr::pluck($this->tags, 'id')),
            'subscribed' => $this->when($this->relationLoaded('subscribed'), fn() => isset($this->subscribed)),
        ]);
    }
}
