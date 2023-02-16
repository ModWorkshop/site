<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LightCollection extends ResourceCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'data';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $arr = $this->resource->toArray();

        return [
            'data' => $arr['data'],
            'meta' => [
                'current_page' => $arr['current_page'],
                'last_page' => $arr['last_page'],
                'total' => $arr['total'],
                'per_page' => $arr['per_page'],
            ]
        ];
    }
}
