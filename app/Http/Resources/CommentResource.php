<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'user' => new UserResource($this->whenLoaded('user')),
            'last_replies' => $this->whenLoaded('replies', fn() => CommentResource::collection($this->last_replies)),
            'subscribed' => $this->whenLoaded('subscribed'),
        ]);
    }
}
