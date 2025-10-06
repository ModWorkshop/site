import { useStore } from '~/store';
import type { File, Link, Mod } from '~/types/models';

export function canEditMod(mod: Mod) {
	const { user, hasPermission } = useStore();

	if (!user) {
		return false;
	}

	if (hasPermission('manage-mods', mod.game) || user?.id === mod.user_id) {
		return true;
	}

	if (!hasPermission('create-mods', mod.game)) {
		return false;
	}

	const membership = mod.members.find(member => member.accepted && member.id === user.id);

	return membership ? (membership.level === 'collaborator' || membership.level === 'maintainer') : false;
}

/**
 * Either owner or moderator basically
 */
export function canSuperUpdate(mod: Mod) {
	const { user, hasPermission } = useStore();

	if (!user || !mod) {
		return false;
	}

	return (mod.user_id === user.id && hasPermission('create-mods', mod.game)) || hasPermission('manage-mods', mod.game);
}

export function registerDownload(downlaod_type: 'file' | 'link', download: File | Link) {
	postRequest(`${downlaod_type}s/${download.id}/register-download`, null);
}

// Bare minimum fields required for a list mod to function for optimization purposes.
export const listModFields = [
	'id',
	'category_id',
	'game_id',
	'user_id',
	'name',
	'short_desc',
	'visibility',
	'downloads',
	'likes',
	'views',
	'suspended',
	'bumped_at',
	'published_at',
	'has_download',
	'approved',
	'thumbnail_id'
];

export const memberLevels = [
	'Maintainer',
	'Collaborator',
	'Viewer',
	'Contributor'
];
