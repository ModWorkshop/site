<?php

namespace App\Http\Middleware;

use App\Models\TrackSession;
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
        $ip = $request->ip();
        $user = $request->user();
        $activeSession = TrackSession::where('ip_address', $ip)->first();

        if (isset($activeSession)) {
            if (isset($user) && $activeSession->user_id != $user->id) {
                $activeSession->user_id = $user->id;
            }
            $activeSession->ip_address = $ip;
            $activeSession->updated_at = Carbon::now();
            $activeSession->save();
        } else {
            $activeSession = TrackSession::create([
                'user_id' => $user?->id,
                'ip_address' => $ip,
                'updated_at' => Carbon::now()
            ]);
        }

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
