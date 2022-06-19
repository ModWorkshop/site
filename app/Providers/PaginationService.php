<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
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
        Builder::macro('getPaginate', function($page = 1, $perPage = null, $columns = ['*']) {
            /**
             * @var Builder $this
            */
        
            $result = $this->paginate($perPage, $columns, null, $page);

            return [
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'data' => $result->items()
            ];
        });
    }
}
