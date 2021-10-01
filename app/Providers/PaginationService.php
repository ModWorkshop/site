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
        Builder::macro('getPaginate', function($items, ...$args) {
            /**
             * @var Builder $this
            */
        
            $result = $this->paginate($items, ...$args);

            return [
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'data' => $result->items()
            ];
        });
    }
}
