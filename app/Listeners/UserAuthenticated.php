<?php

namespace App\Listeners;

use Auth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserAuthenticated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = Auth::user();
        $user->roles->loadMissing('permissions');
        $user->append('permissions');
    }
}
