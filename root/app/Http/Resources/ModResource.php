<?php

namespace App\Http\Resources;

use App\Models\File;
use App\Models\Link;
use Arr;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Resources\MissingValue;

class ModResource extends BaseResource
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

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'game_id' => $this->game_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'desc' => $this->desc,
            'short_desc' => $this->short_desc,
            'changelog' => $this->changelog,
            'license' => $this->license,
            'instructions' => $this->instructions,
            'visibility' => $this->visibility,
            'legacy_banner_url' => $this->legacy_banner_url,
            'downloads' => $this->downloads,
            'likes' => $this->likes,
            'views' => $this->views,
            'version' => $this->version,
            'donation' => $this->donation,
            'suspended' => $this->suspended,
            'comments_disabled' => $this->comments_disabled,
            'score' => $this->score,
            'daily_score' => $this->daily_score,
            'weekly_score' => $this->weekly_score,
            'bumped_at' => $this->bumped_at,
            'published_at' => $this->published_at,
            'download_id' => $this->download_id,
            'download_type' => $this->download_type,
            'last_user_id' => $this->last_user_id,
            'has_download' => $this->has_download,
            'approved' => $this->approved,
            'allowed_storage' => $this->allowed_storage,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'thumbnail_id' => $this->thumbnail_id,
            'banner_id' => $this->banner_id,
            'instructs_template_id' => $this->instructs_template_id,
            'disable_mod_managers' => $this->disable_mod_managers,
            'breadcrumb' => $this->whenAppended('breadcrumb'),
            'download' => $this->whenAppended('download'),
            'mod_managers' => $this->whenAppended('modManagers'),
            'banner' => $this->whenLoaded('banner'),
            'thumbnail' => $this->whenLoaded('thumbnail'),
            'dependencies' => $this->whenLoaded('dependencies'),
            'instructs_template' => $this->whenLoaded('instructsTemplate'),
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
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
        ];
    }
}
