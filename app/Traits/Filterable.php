<?php
namespace App\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Scout\Searchable as ScoutSearchable;

trait Filterable {
    /**
     * Handles searching via query string and paginating to avoid copypasting code.
     * query - use Illuminate\Database\Eloquent\Builder;
     *
     * @param array $val
     * @param \Closure|null $callback
     * @return Paginator
     */
    public static function queryGet(array $val, \Closure $callback = null) : Paginator {
        $query = self::query();

        if (isset($val['query']) && !empty($val['query'])) {
            $query->where(fn($q) => $q->whereRaw('name % ?', $val['query'])->orWhere('name', 'ILIKE', '%'.$val['query'].'%'));
        }

        if (isset($callback)) {
            $query->where(fn($q) => $callback($q, $val));
        }

        return $query->paginate($val['limit'] ?? 50);
    }
}