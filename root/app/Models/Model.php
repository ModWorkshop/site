<?php
namespace App\Models;

// Extend of the regular eloquent model with some convenient/missing functions
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
}
