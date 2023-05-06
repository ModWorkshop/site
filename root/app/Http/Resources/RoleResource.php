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
            'is_vanity' => $this->is_vanity,
            'assignable' => $this->is_vanity || $this->canBeEdited(),
            'permissions' => $this->whenLoaded('permissions', function() {
                $permissions = [];

                foreach ($this->permissions as $permission) {
                    $permissions[$permission->name] = true;
                }

                return (object)$permissions; //Forces JSON to treat this as an object and NOT an array for some dumb reason.
            })
        ];
    }
}
