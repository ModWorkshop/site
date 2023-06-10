<?php

namespace App\Services;

//This class is essentially used to make sure we don't make a static value containing the game
// Which could leak because we use Laravel Optane and let's models know that we currently have a game loaded.
// TODO: I'd probably want things to be more passed to the object rather than have a singleton like this
// While it's currently not done, it's possible two games may need to be loaded at once.

class SiteState {
    public int $currentGame;
    public array $categories;

    // When we are in a route that fetches a single resource (like mods/12345) we want to load the game.
    public bool $showResourceRoute = false;

    function getCurrentGame(): int {
        return $this->currentGame ?? 0;
    }

    function getCategories(): ?array {
        return $this->categories ?? [];
    }
}
