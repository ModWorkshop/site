<?php

namespace App\Services;

use App\Models\Thread;
use Auth;

class ThreadService {
    public static function threads(array $val, callable $querySetup=null, $query=null)
    {
        return ($query ?? Thread::query())->queryGet($val, function($query, array $val) use ($querySetup) {
            if (isset($querySetup)) {
                $querySetup($query, $val);
            }

            self::filters($query, $val);
        });
    }

    // Filters to get threads that the user can see
    public static function filters($query, $val = [])
    {
        $user = Auth::user();

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
