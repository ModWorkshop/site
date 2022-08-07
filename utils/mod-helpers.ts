import { useStore } from "~~/store";
import { Mod } from "~~/types/models";

export function canEditMod(mod: Mod) {
    const { user, hasPermission } = useStore();
    
    if (!user || !hasPermission('edit-own-mod')) {
        return false;
    }

    if (hasPermission('edit-mod') || user.id === mod.user_id) {
        return true;
    }

    const membership = mod.members.find(member => member.accepted && member.id === user.id);
    return membership && membership.level <= 1;
}

/**
 * Either owner or moderator basically
 */
export function canSuperUpdate(mod: Mod) {
    const { user, hasPermission } = useStore();

    return (mod.user_id === user.id && hasPermission('edit-own-mod')) || hasPermission('edit-mod');
}

export const memberLevels = [
    'Maintainer',
    'Collaborator',
    'Viewer',
    'Contributor',
];