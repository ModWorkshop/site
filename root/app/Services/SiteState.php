<?php

namespace App\Services;

use App\Models\Game;
use \App\Models\Role;
use \App\Models\Mod;
use \App\Models\User;

use Cache;

//This class is essentially used to make sure we don't make a static value containing the game
// Which could leak because we use Laravel Optane and let's models know that we currently have a game loaded.
// TODO: I'd probably want things to be more passed to the object rather than have a singleton like this
// While it's currently not done, it's possible two games may need to be loaded at once.

class SiteState {
    public ?Game $currentGame = null;

    public array $mods = [];
    public array $categories;

    public array $users = [];
    public ?Role $membersRole = null;

    // What resource we are showing currently (or not)
    public ?string $showResourceRoute = null;

    // For optimization purposes, if a mod is needed (For example for files) we can avoid loading it again.
    function getMod(int $id): ?Mod {
        return $this->mods[$id];
    }

    function addMod(Mod $mod) {
        $this->mods[$mod->id] = $mod;
    }

    function addUser(User $user) {
        $this->users[$user->id] = $user;
    }

    function setCurrentGame(Game $game){
        $this->currentGame = $game;
        if (isset($game)) {
            $game->append('announcements');
        }
    }

    function getCurrentGame(): ?Game {
        return $this->currentGame;
    }

    function getCategories(): ?array {
        return $this->categories ?? [];
    }

    function getMembersRole(): ?Role
    {
        $this->membersRole ??= Cache::remember('membersRole', 60, function() {
            return Role::with('permissions')->find(1);
        });

        return $this->membersRole;
    }
}
