<?php

namespace App\Traits;

use Illuminate\Contracts\Events\Dispatcher;

/**
 * @property Dispatcher $dispatcher
 * @property array $dispatchesEvents
 */
trait RelationsListener {
    
    /**
     * Fire the given event for the model.
     *
     * @param  string  $event
     * @param  bool  $halt
     * @return mixed
     */
    private function fireModelEventWithArgs($event, $halt = true, ...$args)
    {
        if (! isset(static::$dispatcher)) {
            return true;
        }

        // First, we will get the proper method to call on the event dispatcher, and then we
        // will attempt to fire a custom, object based event for the given event. If that
        // returns a result we can return that result, or we'll call the string events.
        $method = $halt ? 'until' : 'dispatch';

        $result = $this->filterModelEventResults(
            $this->fireCustomModelEvent($event, $method)
        );

        if ($result === false) {
            return false;
        }

        return ! empty($result) ? $result : static::$dispatcher->{$method}(
            "eloquent.{$event}: ".static::class, [$this, ...$args]
        );
    }


    /**
     * Register a model event with the dispatcher.
     *
     * @param  string  $event
     * @param  \Closure|string  $callback
     * @return void
     */
    abstract protected static function registerModelEvent($event, $callback);

    static function relationHasLoaded(callable $callback) {
        static::registerModelEvent('relationHasLoaded', $callback);
    }

    public function setRelation($relation, $value)
    {
        parent::setRelation($relation, $value);

        if (isset($value)) {
            $this->fireModelEventWithArgs('relationHasLoaded', true, $relation, $value);
        }

        return $this;
    }
}