<?php

namespace App\Http\Resources;

use App\Models\File;
use App\Models\Link;
use Arr;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

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
        //Basically nothing, no "key": null just nothing!
        $missingValue = new MissingValue;

        $download = $missingValue;
        if ($this->download_type === 'file') {
            $download = $this->whenLoaded('files', fn() => $this->files->find($this->download_id));
        } else if ($this->download_type === 'link') {
            $download = $this->whenLoaded('links', fn() => $this->links->find($this->download_id));
        }

        return array_merge(parent::toArray($request), [
            'user' => new UserResource($this->user),
            'files' => $this->whenLoaded('files'),
            'links' => $this->whenLoaded('links'),
            'images' => $this->whenLoaded('images'),
            'members' => $this->whenLoaded('members', function() use ($missingValue, $request) {
                $members = [];
                foreach ($this->members as $member) {
                    $memberCopy = (new UserResource($member))->toArray($request);
                    $memberCopy['accepted'] = $member->pivot->accepted;
                    $memberCopy['level'] = $member->pivot->level;
                    $memberCopy['pivot'] = $missingValue;

                    $members[] = $memberCopy;
                }

                return $members;
            }),
            'transfer_request' => $this->whenLoaded('transferRequest'),
            'last_user' => $this->whenLoaded('lastUser'),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'download' => $download, 
            'tag_ids' => $this->whenLoaded('tags', fn () => Arr::pluck($this->tags, 'id')),
            'liked' => $this->whenLoaded('liked', fn () => isset($this->liked)),
        ]);
    }
}
