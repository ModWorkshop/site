<?php

namespace App\Providers;

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

        Builder::macro('queryGet', function(array $val, \Closure $callback = null, $useTrigrams=false) {
            /**
             * @var Builder $this
            */

            if (isset($val['query']) && !empty($val['query'])) {
                $query = $val['query'];
                //As far as I know searching with less than 3 characters using trigrams is VERY slow
                if ($useTrigrams && mb_strlen($query) > 2) {
                    $this->where(fn($q) => $q->whereRaw('name % ?', $val['query'])->orWhere('name', 'ILIKE', '%'.$val['query'].'%'));
                } else {
                    $this->where('name', 'ILIKE', '%'.$val['query'].'%');
                }
            }

            if (isset($callback)) {
                $callback($this, $val);
            }

            return $this->paginate($val['limit'] ?? 50);
        });
    }
}
