<?php

namespace App\Services;

use App\Models\ForumCategory;
use App\Models\Thread;
use Arr;
use Auth;
use Chr15k\MeilisearchAdvancedQuery\MeilisearchQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class ThreadService {
    public static function threads(array $val, callable $querySetup=null, $query=null)
    {
        return ($query ?? Thread::query()->forListing())->queryGet($val, function($query, array $val) use ($querySetup) {
            if (isset($querySetup)) {
                $querySetup($query, $val);
            }
            self::filters($query, $val);
        });
    }

    public static function meiliSearch($val) {
        $user = Auth::user();
        $search = MeilisearchQuery::for(Thread::class);

        if (isset($val['closed'])) {
            $search->where('closed', $val['closed'] == 1);
        }

        $noPins = Arr::get($val, 'no_pins', false);
        if ($noPins) {
            $search->sort('bumped_at:desc');
        } else {
            $search->sort(['pinned_at:desc', 'bumped_at:desc']);
        }

        $forumId = Arr::get($val, 'forum_id');

        if (!isset($user) || !$user->hasPermission('manage-discussions')) {
            $search->where(function($q) use ($user, $forumId) {
                $forumCats = ForumCategory::select('id')
                    ->when($forumId, fn($q, $forumId) => $q->where('forum_id', $forumId))
                    ->where(fn($q) => Utils::forumCategoriesFilter($q, true));

                if (isset($user)) {
                    return $q->where('user_id', $user?->id)
                    ->orWhere('category_id', 'IS NULL')
                    ->orWhereIn('category_id', $forumCats->pluck('id')->toArray());
                } else {
                    return $q->Where('category_id', 'IS NULL')
                        ->orWhereIn('category_id', $forumCats->pluck('id')->toArray());
                }
            });
        }

        if (isset($val['category_id'])) {
            $search->where('category_id', $val['category_id']);
        }

        if (isset($val['category_name'])) {
            $search->where('category_name', $val['category_name']);
        }

        if (isset($forumId)) {
            $search->where('forum_id', $forumId);
        }

        if (!empty($val['tags'])) {
            $search->whereIn('tag_ids', $val['tags']);
        }

        $limit = Arr::get($val, 'limit', 20);
        $builder = $search->search($val['query'] ?? '');

        $builder->query(function(Builder $q) use ($builder) {
            $q->forListing();
            $builder->query(null); // A hack to prevent Scout from trying to count it via DB
        });
        return $builder->paginate(Number::clamp($limit, 1, 100));
    }

    // Filters to get threads that the user can see
    public static function filters($query, $val = [])
    {
        $user = Auth::user();

        if (isset($val['closed'])) {
            $query->where('closed', $val['closed']);
        }

        if (isset($val['no_pins']) && $val['no_pins']) {
            $query->orderByDesc('bumped_at');
        } else {
            $query->orderByRaw('pinned_at DESC NULLS LAST, bumped_at DESC');
        }

        if (!isset($user) || !$user->hasPermission('manage-discussions')) {
            $query->where(function($q) use ($user) {
                $q->where('threads.user_id', $user?->id)->orWhereNull('category_id')->orWhereRelation('category', fn($q) => Utils::forumCategoriesFilter($q, true));
            });
        }

        $query->where(function($query) use ($val) {
            if (isset($val['category_id'])) {
                $query->where('category_id', $val['category_id']);
            }
            if (isset($val['category_name'])) {
                $query->orWhereRelation('category', fn($q) => $q->where('name', $val['category_name']));
            }
            if (isset($val['forum_id'])) {
                $query->where('forum_id', $val['forum_id']);
            }

            if (!empty($val['tags'])) {
                $query->whereHasIn('tagsSpecial', function($q) use ($val) {
                    $q->whereIn('taggables.tag_id', array_map('intval', $val['tags']));
                });
            }
        });

        return $query;
    }


}
