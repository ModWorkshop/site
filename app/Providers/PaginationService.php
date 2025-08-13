<?php

namespace App\Providers;

use Arr;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        // Figure this

        // Model::resolveRelationUsing('queryGet', function(Model $model, array $val, \Closure $callback = null) {
        //     $model->query()->queryGet($val, $callback);
        // });

        Builder::macro('queryGet', function(array $val, Closure $callback = null, $useTrigrams=false) {
            /**
             * @var Builder $this
            */

            if (isset($val['query']) && !empty($val['query'])) {
                $query = trim($val['query']);
                
                if ($useTrigrams && mb_strlen($query) > 2) {
                    $this->where(function($q) use ($query) {
                        $tsquery = str_replace(' ', ' & ', $query);
                        $q->whereRaw("name @@ to_tsquery(?)", [$tsquery])
                            ->orWhereRaw("levenshtein(name, ?) <= 4", [$query])
                            ->orWhereRaw("similarity(name, ?) > 0.3", [$query])
                            ->orWhereRaw("name ILIKE ?", ['%' . $query . '%']);
                    });
                } else {
                    $this->whereRaw("name ILIKE '%' || ? || '%'", $query);
                }
            }

            $ids = Arr::pull($val, 'ids');
            if (isset($ids) && is_array($ids) && !empty($ids)) {
                $this->whereIn('id', $ids);
            }

            if (isset($callback)) {
                $callback($this, $val);
            }

            return $this->paginate($val['limit'] ?? 50);
        });
    }
}
