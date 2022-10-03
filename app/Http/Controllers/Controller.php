<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Error;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct() {
        //Make sure we don't accidentally add a controller without one lol
        throw new Error('You must implement policy for ' . $this::class);
    }

    public function authorizeGameResource(string $class, string $resource)
    {
        $request = request();
        $game = $request->route('game');

        if (isset($game)) {
            $this->authorizeResource([$class, 'game'], "{$resource}, game");
            User::setCurrentGame($game);
        } else {
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
