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
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $url = env('FRONTEND_URL');
        if ($request->header('Referer') === $url || $request->header('Origin') === $url) {
            $ip = $request->ip();
            $user = $request->user();

            $session = TrackSession::where('ip_address', $ip)->first();
            $now = Carbon::now();
            if (isset($session)) {
                if ($session->updated_at->diffInSeconds($now) > 60) {
                    $session->update([
                        'user_id' => $user?->id,
                        'updated_at' => $now
                    ]);
                }
            } else {
                TrackSession::create([
                    'ip_address' => $ip,
                    'user_id' => $user?->id,
                    'updated_at' => $now
                ]);
            }

            // Update the last online every 5 minutes or so.
            if (isset($user)) {
                if (!isset($user->last_online) || $user->last_online->diffInSeconds($now) > 60) {
                    $user->update([
                        'last_online' => $now,
                        'last_ip_address' => $request->ip
                    ]);
                }
            }
        }

        return $next($request);
    }
}
