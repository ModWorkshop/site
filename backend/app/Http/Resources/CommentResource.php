<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class CommentResource extends BaseResource
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
