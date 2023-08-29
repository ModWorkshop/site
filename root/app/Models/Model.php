<?php
namespace App\Models;

// Extend of the regular eloquent model with some convenient/missing functions
class Model extends \Illuminate\Database\Eloquent\Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::retrieved(function($model) {
            $game = app('siteState')->currentGame;

            // This prevents loading the game again when we already loaded it
            if ($model->game_id == $game?->id) {
                $model->setRelation('game', $game);
                Mod::preventLazyLoading();
                $model->without(['game']);
            }

//            if (isset($model->user_id)) {
//                $user = app('siteState')->users[$model->user_id] ?? null;
//                if (isset($user)) {
//                    $model->setRelation('user', $user);
//                    $model->earlyWithout('user');
//                }
//            }
        });



    }

    /**
     * Removes a relation from $this->with rather than eagerLoad, useful in cases that eagerLoad hasn't been populated.
     */
    function earlyWithout(string $name)
    {
        unset($this->with[$name]); // In case it's defined as a key
        $this->with = array_filter($this->with, fn($item) => $item != $name);
        $this->without($name);

        return $this;
    }
}
