<?php

namespace App\Http\Resources;

use Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tag' => $this->tag,
            'color' => $this->color,
            'order' => $this->order,
            'permissions' => $this->whenLoaded('permissions', fn() => Arr::pluck($this->permissions, 'slug'))
        ];
    }
}
