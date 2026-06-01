<?php

namespace App\Providers;

use App\Services\APIService;
use Arr;
use Cache;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class PaginationService extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('queryGet', function(array $val, Closure $callback = null, ?array $cache=null) {
            $getFunc = function(Builder $q) use ($val, $callback) {
                if (isset($val['query']) && !empty($val['query'])) {
                    $q->whereRaw("name ILIKE '%' || ? || '%'", [trim($val['query'])]);
                }

                $ids = Arr::pull($val, 'ids');
                if (isset($ids) && is_array($ids) && !empty($ids)) {
                    $q->whereIn('id', $ids);
                }

                if (isset($callback)) {
                    $callback($q, $val);
                }

                $limit = Arr::get($val, 'limit', 20);
                return $q->paginate(Number::clamp($limit, 1, 100));
            };

            if ($cache === null) {
                return $getFunc($this);
            } else {
                return Cache::remember($cache['key'].APIService::hashByQuery(), $cache['ttl'], fn() => $getFunc($this));
            }
        });
    }
}
