<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Services\APIService;
use Auth;
use Error;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct() {
        //Make sure we don't accidentally add a controller without one lol
        throw new Error('You must implement policy for ' . $this::class);
    }

    public function authorizeGameResource(string $class, string $resource)
    {
        $game = app(Game::class)->resolveRouteBinding(request()->route('game'));

        if (isset($game)) {
            $this->authorizeResource([$class, 'game'], "{$resource}, game");
            APIService::setCurrentGame($game);
        } else {
            if (!empty(request()->route($resource)) && property_exists($class, 'game_id')) {
                $class::retrieved(function($model) {
                    APIService::setCurrentGame($model->game);
                    if (method_exists($model, 'withFetchResourceGame')) {
                        $model->withFetchResourceGame();
                    }
                });
            }

            $this->authorizeResource($class, $resource);
        }
    }

    public function user() : ?User
    {
        return Auth::user();
    }

    public function userId() : ?int
    {
        return Auth::user()?->id;
    }
}
