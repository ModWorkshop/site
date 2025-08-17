<?php
namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;

// Extend of the regular eloquent model with some convenient/missing functions
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 * @mixin \Eloquent
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    public static $preventGameEagerLoad = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // This *tries* to solve games getting loaded twice (because we need to interact with them in Controller)
        // Sadly Laravel doesn't really stop the load if we try to call ->without or try removing it
        // In the 'retrieved' event. If you have a better solution, feel free to change this.
        if (self::$preventGameEagerLoad) {
            if (method_exists($this, 'game')) {
                $needsGame = array_search('game', $this->with);
                if ($needsGame) {
                    $this->with = array_filter($this->with, fn ($a) => $a != 'game');
                }

                static::retrieved(function($model) use ($needsGame) {
                    $game = app('siteState')->currentGame;
                    if ($needsGame && isset($game) && $model->game_id == $game?->id) {
                        $model->setRelation('game', $game);
                    }
                });
            }
        }
    }

    protected function getRelationshipFromMethod($method)
    {
        return $this->withSecureConstraints(function() use ($method) {
            return parent::getRelationshipFromMethod($method);
        });
    }

    /**
     * Override the getAttribute method to force constraints for relationship access
     */
    public function getAttribute($key)
    {
        // Check if this is a relationship and constraints are disabled
        if ($this->isRelation($key)) {
            return $this->withSecureConstraints(function() use ($key) {
                return parent::getAttribute($key);
            });
        }

        return parent::getAttribute($key);
    }

    /**
     * Execute a callback with constraints temporarily enabled if they're currently disabled
     * 
     * Hopefully fixes this issue https://github.com/laravel/framework/issues/51825
     * Thanks Laravel for focusing on useless crappy sugar and not fixing security issues!!!!
     */
    public function withSecureConstraints(callable $callback)
    {
        if (!Relation::_constraints()) {
            // Temporarily enable constraints
            Relation::_setConstraints(true);

            try {
                return $callback();
            } finally {
                // Restore the disabled constraints state
                Relation::_setConstraints(false);
            }
        }

        return $callback();
    }

}
