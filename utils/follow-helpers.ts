import { Game } from './../types/models';
import { Mod } from '~~/types/models';
import { User } from '../types/models';
export async function setFollowUser(user: User, notify?: boolean) {
    try {
        if (!user.followed) {
            await usePost('followed-users', { user_id: user.id, notify });
            user.followed = { notify: false };
        } else {
            await useDelete(`followed-users/${user.id}`);
            user.followed = null;
        }
    } catch (error) {
        console.log(error);
    }
}

export async function setFollowMod(mod: Mod, notify?: boolean) {
    try {
        if (!mod.followed) {
            await usePost('followed-mods', { mod_id: mod.id, notify });
            mod.followed = { notify: false };
        } else {
            await useDelete(`followed-mods/${mod.id}`);
            mod.followed = null;
        }
    } catch (error) {
        console.log(error);
    }
}

export async function setFollowGame(game: Game) {
    try {
        if (!game.followed) {
            await usePost('followed-games', { game_id: game.id });
            game.followed = true;
        } else {
            await useDelete(`followed-games/${game.id}`);
            game.followed = false;
        }
    } catch (error) {
        console.log(error);
    }
}