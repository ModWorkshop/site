<?php
namespace App\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Scout\Searchable as ScoutSearchable;

trait Filterable {
    use ScoutSearchable;

    /**
     * Handles searching via query string and paginating to avoid copypasting code.
     *
     * @param array $val
     * @param \Closure|null $callback
     * @return Paginator
     */
    public static function queryGet(array $val, \Closure $callback = null) : Paginator {
        $query = null;
        if (isset($val['query']) && !empty($val['query'])) {
            $query = self::search($val['query']);
        } else {
            $query = self::query();
        }

        if (isset($callback)) {
            $callback($query, $val);
        }

        return $query->paginate($val['limit'] ?? 50);
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name
        ];
    }
}