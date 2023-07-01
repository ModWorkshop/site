<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = $request->user();

        if (!isset($user)) {
            abort(401);
        }

        $permissions = explode(',', $permissions);

        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                abort(403);
            }
        }

        return $next($request);
    }
}
