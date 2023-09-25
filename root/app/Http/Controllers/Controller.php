<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Model;
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

    private function handleGameModel(string $class, string $resource, $id) {
        $called = false;
        $class::retrieved(function($model) use (&$called, $resource, $id) {
            if ($model->id == $id) {
                if (!$called) {
                    if (isset($model->game_id)) {
                        APIService::setCurrentGame($model->game);
                    }
                    $called = true; // TODO: maybe we need to run this more than once? find a better way.

                    if (app('siteState')->showResourceRoute == $resource && method_exists($model, 'withFetchResourceGame')) {
                        $model->withFetchResourceGame();
                    }
                }
            }
        });
    }

    // Authorizes a resource known to be owned directly by a game (has game_id and accessible via /game/ route when creating)
    public function authorizeGameResource(string $class, string $resource=null)
    {
        $resource ??= app($class)->getMorphClass();
        $game = app(Game::class)->resolveRouteBinding(request()->route('game'));

        if (isset($game)) {
            APIService::setCurrentGame($game);
            $this->authorizeResource([$class, 'game'], "{$resource}, game");
        } else {
            $id = request()->route($resource);
            if (!empty($id)) {
                app('siteState')->showResourceRoute = $resource;
                Model::$preventGameEagerLoad = true;
                $this->handleGameModel($class, $resource, $id);
            }

            $this->authorizeResource($class, $resource);
        }
    }

    // Like authorizeGameResource but more general for mods, threads, etc to be able to capture the game_id and set it
    public function authorizeWithParentResource(string $class, string $parentClass, string $resource=null, string $parentResource=null) {
        $resource = app($class)->getMorphClass();
        $parentResource = app($parentClass)->getMorphClass();
        $parentId = request()->route($parentResource);

        if (isset($parentId)) {
            Model::$preventGameEagerLoad = true;

            $this->handleGameModel($parentClass, $parentResource, $parentId);
            $this->authorizeResource([$class, $parentResource], "{$resource}, {$parentResource}");
        } else {
            if (!empty(request()->route($resource))) {
                app('siteState')->showResourceRoute = $resource;

                $resourceModel = app($class)->resolveRouteBinding(request()->route($resource));

                if (isset($resourceModel)) {
                    $parent = $resourceModel[$parentResource];
                    if (isset($parent->game_id) && $parent->game_id) {
                        APIService::setCurrentGame($parent->game);
                    }
                }
            }

            $this->authorizeResource($class, $resource);
        }
    }

    // Authorizes and handles game setting for resources that don't know their parent at compile time.
    // Example: comments that use commentable as morph name
    public function authorizeWithMorphParentResource(string $class, string $parentMorphName, string $resource=null) {
        $resource ??= app($class)->getMorphClass();

        if (!empty(request()->route($resource))) {
            app('siteState')->showResourceRoute = $resource;

            $called = false;
            $class::retrieved(function($model) use (&$called, $parentMorphName) {
                if (!$called) {
                    if (isset($model[$parentMorphName]) && isset($model[$parentMorphName]->game_id)) {
                        APIService::setCurrentGame($model[$parentMorphName]->game);
                    }
                    $called = true; // TODO: maybe we need to run this more than once? find a better way.
                    if (method_exists($model, 'withFetchResourceGame')) {
                        $model->withFetchResourceGame();
                    }
                }

            });
        }

        $this->authorizeResource($class, $resource);
    }

    /**
     * Returns currently autohrized user
     */
    public function user() : ?User
    {
        return Auth::user();
    }

    /**
     * Returns currently autohrized user ID
     */
    public function userId() : ?int
    {
        return Auth::user()?->id;
    }
}
