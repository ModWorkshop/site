<?php

namespace App\Http\Resources;

use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * Returns a collection response that is trimmed down.
     * Removed links due them being a waste of space that isn't even accurate.
     */
    public static function collectionResponse($resource): array
    {
        return APIService::paginatedResponse(self::collection($resource));
    }
}
