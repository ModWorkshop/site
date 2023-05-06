<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var User
         */
        $user = $request->user();

        // Update the last online every 5 minutes or so.
        if (isset($user)) {
            if (!isset($user->last_online) || $user->last_online->diffInMinutes(Carbon::now()) > 1) {
                $user->update([
                    'last_online' => Carbon::now(),
                    'last_ip_address' => $request->ip
                ]);
            }
        }

        return $next($request);
    }
}
