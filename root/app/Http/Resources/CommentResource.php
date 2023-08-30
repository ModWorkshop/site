<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'user' => new UserResource($this->user),
            'last_replies' => $this->whenLoaded('lastReplies', fn() => CommentResource::collection($this->lastReplies)),
            'subscribed' => $this->whenLoaded('subscribed'),
        ]);
    }
}
