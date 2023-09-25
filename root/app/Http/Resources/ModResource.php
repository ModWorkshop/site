<?php

namespace App\Http\Resources;

use App\Models\File;
use App\Models\Link;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class ModResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //Basically nothing, no "key": null just nothing!
        $missingValue = new MissingValue;

        return array_merge(parent::toArray($request), [
            'user' => new UserResource($this->whenLoaded('user')),
            'files' => $this->whenLoaded('files', fn() => new LightCollection($this->files()->paginate(5))),
            'links' => $this->whenLoaded('links', fn() => new LightCollection($this->links()->paginate(5))),
            'files_count' => $this->when($this->fullLoad, fn() => $this->filesCount),
            'links_count' => $this->when($this->fullLoad, fn() => $this->linksCount),
            'images' => $this->whenLoaded('images'),
            'followed' => $this->whenLoaded('followed'),
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
            'game' => $this->whenLoaded('game', fn() => $this->fullLoad ? new GameResource($this->game) : new SmallGameResource($this->game)),
            'category' => $this->whenLoaded('category', fn() => $this->fullLoad ? new CategoryResource($this->category) : new SmallCategoryResource($this->category)),
            'transfer_request' => $this->whenLoaded('transferRequest'),
            'last_user' => $this->whenLoaded('lastUser', fn() => new UserResource($this->lastUser)),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'tag_ids' => $this->whenLoaded('tags', fn () => Arr::pluck($this->tags, 'id')),
            'liked' => $this->whenLoaded('liked'),
            'subscribed' => $this->whenLoaded('subscribed', fn() => isset($this->subscribed)),
        ]);
    }
}
