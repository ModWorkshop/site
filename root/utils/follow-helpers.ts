import type { Game, Mod, User } from '~~/types/models';
export async function setFollowUser(user: User, notify?: boolean, status?: boolean) {
    try {
        status ??= !user.followed;
        if (status) {
            await postRequest('followed-users', { user_id: user.id, notify });
            user.followed = { notify: false };
        } else {
            await deleteRequest(`followed-users/${user.id}`);
            user.followed = undefined;
        }
    } catch (error) {
        console.log(error);
    }
}

export async function setFollowMod(mod: Mod, notify?: boolean, status?: boolean) {
    try {
        status ??= !mod.followed;
        if (status) {
            await postRequest('followed-mods', { mod_id: mod.id, notify });
            mod.followed = { notify: false };
        } else {
            await deleteRequest(`followed-mods/${mod.id}`);
            mod.followed = undefined;
        }
    } catch (error) {
        console.log(error);
    }
}

export async function setFollowGame(game: Game, status?: boolean) {
    try {
        status ??= !game.followed;
        if (status) {
            await postRequest('followed-games', { game_id: game.id });
            game.followed = true;
        } else {
            await deleteRequest(`followed-games/${game.id}`);
            game.followed = false;
        }
    } catch (error) {
        console.log(error);
    }
}