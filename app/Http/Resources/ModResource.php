<?php

namespace App\Http\Resources;

use App\Models\File;
use Arr;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * Since we don't want to give the API something like download_type = App\Models\File
 * we just convert it
 */
const downloadTypeSimple = [
    File::class => 'file',
];

class ModResource extends JsonResource
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
            'submitter' => new UserResource($this->submitter),
            'files' => $this->files,
            'images' => $this->images,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'download' => $this->whenLoaded('files', function() {
                if ($this->download_type === File::class) {
                    return $this->files->find($this->download_id);
                }
            }),
            'download_type' => downloadTypeSimple[$this->download_type] ?? null,
        ]);
    }
}
