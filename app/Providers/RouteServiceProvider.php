<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    protected const MODEL_ID_BINDINGS = [
        'user',
        'tag',
        'game',
        'category',
        'role',
        'file',
        'image',
        'comment',
        'link',
        'case',
        'modMember',
        'thread',
        'mod',
        'ban',
        'notification'
    ];


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        //https://github.com/laravel/framework/issues/26239)
        //Thank you Laravel for not fixing this when it should be.
        //Ah yes SQL errors, why yes it makes entirely sense to keep that behavior!
        Route::patterns(array_merge(array_fill_keys(self::MODEL_ID_BINDINGS, '[0-9]{1,10}')));

        $this->routes(function () {
            Route::middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            return Limit::perMinute(isset($user) ? 120 : 60)->by(optional($user)->id ?: $request->ip());
        });
    }
}
