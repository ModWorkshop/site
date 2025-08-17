<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AuditLogResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'data' => $this->getAttribute('data')
        ];
    }
}
