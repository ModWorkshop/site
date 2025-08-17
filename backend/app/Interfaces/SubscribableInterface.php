<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface SubscribableInterface {
    public function subscriptions() : MorphMany;
}