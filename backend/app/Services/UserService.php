<?php

use App\Models\User;
use Chr15k\MeilisearchAdvancedQuery\MeilisearchQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class UserService {
    public static function meilisearch(array $val) {
        $search = MeilisearchQuery::for(User::class);
        // $q->withCount('viewableMods');

        if (isset($val['ids'])) {
            $search->whereIn('id', $val['ids']);
        }

        if (isset($query) && !empty($query)) {
            if (ctype_digit($query) && $query < PHP_INT_MAX) {
                $search->where('id', $query);
            }
        }

        if (isset($val['role_ids'])) {
            $roleIds = array_map('intval', array_filter($val['role_ids'], fn($id) => $id != 1));
            if (!empty($roleIds)) {
                $search->whereIn('role_ids', $roleIds);
            }
        }

        if (isset($game) && isset($val['game_role_ids'])) {
            $roleIds = array_map('intval', $val['game_role_ids']);
            if (!empty($roleIds)) {
                $search->whereIn('game_role_ids', $roleIds);
            }
        }

        $query = $val['query'] ?? '';

        if (empty($query)) {
            $search->sort('id:asc');
        }

        $limit = Arr::get($val, 'limit', 20);
        $users = $search->search($query)->paginate(Number::clamp($limit, 1, 100));
        return $users;
    }

    /**
     * Returns a list of users
     *
     * NOTE: By default when no filters are set (or query is too short),
     * we return the result using the DB instead, this is due to maxTotalHits value of users being low
     * As user search isn't as important as mods.
     *
     * @param array $val
     * @return void
     */
    public static function users(array $val) {
        $query = Arr::get($val, 'query');
        $roleIds = Arr::get($val, 'role_ids');
        $gameRoleIds = Arr::get($val, 'game_role_ids');

        if (mb_strlen($query) > 2 || !empty($roleIds) || !empty($gameRoleIds)) {
            return self::meilisearch($val);
        } else {
            return self::dbFilteredUsers($val);
        }
    }

    public static function dbFilteredUsers(array $val) {
        return User::queryGet($val, fn($q, $val) => self::dbFilters($q, $val));
    }

    public static function dbFilters(Builder $q, array $val) {
        $q->withCount('viewableMods');

        if (isset($val['id'])) {
            $q->where('id', $val['id']);
        }
        if (isset($query) && !empty($query)) {
            if (ctype_digit($query) && $query < PHP_INT_MAX) {
                $q->where('id', $query);
            }
            if (mb_strlen($query) > 2) {
                $q->orWhere(fn($q) => $q->whereRaw('unique_name % ?', $query)->orWhereRaw("unique_name LIKE '%' || ? || '%'", Str::lower($query)));
                $q->orWhere(fn($q) => $q->whereRaw('name % ?', $query)->orWhereRaw("name ILIKE '%' || ? || '%'", $query));
            }
        }
        if (isset($val['role_ids'])) {
            $roleIds = array_filter($val['role_ids'], fn($id) => $id != 1);
            if (!empty($roleIds)) {
                $q->whereHasIn('roles', fn($q) => $q->whereIn('roles.id', $val['role_ids']));
            }
        }
        if (isset($game) && isset($val['game_role_ids'])) {
            $roleIds = $val['game_role_ids'];
            if (!empty($roleIds)) {
                $q->whereHasIn('gameRoles', fn($q) => $q->whereIn('game_roles.id', $val['game_role_ids']));
            }
        }

        if (isset($query) && mb_strlen($query) > 2) {
            if (ctype_digit($query) && $query < PHP_INT_MAX) {
                $q->orderByRaw("
                    id = CAST($1 AS INTEGER) DESC,
                    unique_name = $1 DESC,
                    unique_name ILIKE '%' || $1 || '%' DESC,
                    unique_name % $1 DESC,
                    name ILIKE '%' || $1 || '%' DESC,
                    name % $1 DESC
                ");
            } else {
                $q->orderByRaw("
                    unique_name = $1 DESC,
                    unique_name ILIKE '%' || $1 || '%' DESC,
                    unique_name % $1 DESC,
                    name ILIKE '%' || $1 || '%' DESC,
                    name % $1 DESC
                ");
            }
        }

        $q->orderBy('id');
    }
}
