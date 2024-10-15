<?php
namespace App\Traits;

use App\Models\Subscription;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Subscribable {
    public function subscriptions() : MorphMany
    {
        /** @var Model $this */

        return $this->morphMany(Subscription::class, 'subscribable');
    }

    public function subscribed()
    {
        /** @var Model $this */
    
        return $this->morphOne(Subscription::class, 'subscribable')->where('user_id', Auth::id());
    }
}