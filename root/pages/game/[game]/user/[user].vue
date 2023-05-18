<template>
    <page-block :game="game" size="2xs" game-banner>
        <content-block>
            <a-user :user="user"/>
            <flex gap="3" class="p-4 input-bg" column>
                <a-tag-selector
                    v-model="user.role_ids"
                    multiple
                    :label="$t('role')"
                    :options="roles?.data"
                    :disabled="user.id !== me!.id && !hasPermission('manage-roles')"
                    :enabled-by="role => role.assignable"
                    :color-by="item => item.color"
                    @update:model-value="prepareSaveRoles"
                />
                <a-tag-selector 
                    v-model="user.game_role_ids"
                    :label="$t('game_roles')"
                    multiple
                    :disabled="user.id !== me!.id && !hasPermission('manage-roles', game)"
                    :options="gameRoles?.data" 
                    :enabled-by="role => role.assignable"
                    :color-by="item => item.color"
                    @update:model-value="prepareSaveGameRoles"
                />
            </flex>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { Role, User } from '~/types/models';
import { useStore } from '~~/store';

definePageMeta({ alias: '/g/:game/user/:user' });

const { user: me, hasPermission } = useStore();

const { data: game } = await useResource<User>('game', 'games');
const { data: user } = await useResource<User>('user', `games/${game.value.id}/users`);
const { data: roles } = await useFetchMany<Role>('roles');

const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });

const { data: gameRoles } = await useFetchMany<Role>(() => `games/${game.value.id}/roles`, { immediate: !!game.value });
    async function saveGameRoles() {
    await patchRequest(`games/${game.value.id}/users/${user.value.id}/roles`, { role_ids: user.value?.game_role_ids });
}

async function saveRoles() {
    await patchRequest(`users/${user.value.id}/roles`, { role_ids: user.value.role_ids });
}

watch(user.value.role_ids, racalculateUserStuff);
watch(user.value.game_role_ids, racalculateUserStuff);

const hasRoles = computed(() => roles.value?.data.filter(role => user.value.role_ids.includes(role.id)));
const hasGameRoles = computed(() => gameRoles.value?.data.filter(role => user.value.game_role_ids.includes(role.id)));

function racalculateUserStuff() {
    if (!roles.value || !gameRoles.value) {
        return;
    }

    const firstVanity = (param, roles?: Role[]) => roles?.find(role => role[param] && role.is_vanity);
    const firstRegular = (param, roles?: Role[]) => roles?.find(role => role[param] && !role.is_vanity);

    if (!user.value.custom_color) {
        user.value.color = 
            firstVanity('color', hasGameRoles.value)?.color 
         ?? firstVanity('color', hasRoles.value)?.color
         ?? firstRegular('color', hasGameRoles.value)?.color
         ?? firstRegular('color', hasRoles.value)?.color;
    }

    if (user.value.show_tag !== 'none' || (!user.value.supporter || user.value.show_tag !== 'supporter_or_role')) {
        user.value.tag = 
            firstRegular('tag', hasRoles.value)?.tag
         ?? firstRegular('tag', hasGameRoles.value)?.tag
         ?? firstVanity('tag', hasGameRoles.value)?.tag 
         ?? firstVanity('tag', hasRoles.value)?.tag;
    }
}
</script>