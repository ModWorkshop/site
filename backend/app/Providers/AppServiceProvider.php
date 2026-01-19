<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\APIService;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator as ValidationValidator;
use Request;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('TELESCOPE_ENABLED')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Relation::macro('_constraints', fn() => self::$constraints);
        Relation::macro('_setConstraints', fn($val) => self::$constraints = $val);

        Validator::extendImplicit('min_strict', function ($attribute, $value, $parameters, ValidationValidator $validator) {
            // Only allow null values when nullable rule is used
            if (is_null($value)) {
                return $validator->hasRule($attribute, 'Nullable');
            }

            $clean = APIService::normalizeString($value);
            return mb_strlen($clean) >= (int)$parameters[0];
        }, 'The :attribute must be at least :min characters.');

        Validator::replacer('min_strict', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':min', $parameters[0], $message);
        });

        Validator::extend('email_or_url', function ($attribute, $value, $parameters, ValidationValidator $validator) {
            if (!$validator->validateEmail($attribute, $value, ['rfc']) && !$validator->validateUrl($attribute, $value)) {
                return false;
            }
            return true;
        }, 'Invalid email or URL');

        Validator::extend('is_image', function ($attribute, $value, $parameters, ValidationValidator $validator) {
            if (!($value instanceof \Illuminate\Http\UploadedFile)) {
                return false;
            }

            $allowedMimes = ['image/png', 'image/webp', 'image/avif', 'image/gif', 'image/jpeg'];
            $maxSizeKb = Setting::getValue('image_max_file_size'); // value in KB

            // Check MIME type
            if (!in_array($value->getMimeType(), $allowedMimes)) {
                return false;
            }

            // Check size (in KB)
            if ($value->getSize() / 1024 > $maxSizeKb) {
                return false;
            }

            return true;
        }, 'The provided file is not a valid image file!');

        Validator::extend('spam_check', function ($attribute, $value, $parameters, ValidationValidator $validator) {
            $user = Auth::user();
            $trustLevel = $user->getTrustLevel();
            if ($trustLevel < 12) {
                if ($user->getAccountAgeInHours() < 1 && APIService::countLinks($value) > 0) {
                    return false;
                }
                elseif (APIService::checkSpamContent($value)) {
                    abort(422, "{$attribute}: contains spam content!");
                }
            }

            return true;
        }, 'New accounts cannot post links!');




        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }
}
