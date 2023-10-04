<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use JsonSerializable;

class ForumResource extends BaseResource
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
            'game' => new GameResource($this->game),
        ]);
    }
}
