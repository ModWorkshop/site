import { useStore } from './../store/index';
import { Thread, Game } from './../types/models';

export default function(thread: Thread, game: Game) {
    const { user, hasPermission } = useStore();
    
    if (!hasPermission('create-threads', game) && thread.id) {
        return false;
    }

    if (hasPermission('manage-discussions', game) || user.id === thread.user_id) {
        return true;
    }

    return false;
}