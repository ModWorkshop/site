import { useStore } from '~/store/index';
import type { Thread, Game } from '~/types/models';

export default function (thread: Thread, game?: Game) {
	const { user, hasPermission } = useStore();

	if (!user || !hasPermission('create-discussions', game)) {
		return false;
	}

	if (hasPermission('manage-discussions', game) || user.id === thread.user_id) {
		return true;
	}

	return false;
}
