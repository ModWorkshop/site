<template>
    <flex style="width: 350px;">
        <content-block alt-background padding="5" grow>
            <span class="text-xl">
                <a-user :user="user" avatar-size="lg" :mini-profile="false"/>
            </span>
            <a-tag-selector
                v-model="user.role_ids"
                :label="$t('roles')"
                :options="roles?.data"
                :disabled="user.id !== me.id && !hasPermission('manage-roles')"
                :option-enabled="role => role.assignable"
                @update:model-value="prepareSaveRoles"
            />
            <a-tag-selector 
                v-if="currentGame"
                v-model="user.game_role_ids"
                :disabled="user.id !== me.id && !hasPermission('manage-roles', currentGame)"
                :label="$t('game_roles')"
                :options="gameRoles?.data" 
                :option-enabled="role => role.assignable"
                @update:model-value="prepareSaveGameRoles"
            />
        </content-block>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { User, Role } from '~~/types/models';

const props = defineProps<{
    user: User
}>();

const { currentGame, hasPermission, user: me } = useStore();

const { data: gameRoles } = await useFetchMany<Role>(() => `games/${currentGame?.id}/roles`, { initialCache: true, immediate: !!currentGame });
const { data: roles } = await useFetchMany<Role>('roles', {  params: { only_assignable: 1 }, initialCache: true });

const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });

async function saveGameRoles() {
    await usePatch(`games/${currentGame.id}/users/${props.user.id}/roles`, { role_ids: props.user.game_role_ids });
}

async function saveRoles() {
    await usePatch(`users/${props.user.id}/roles`, { role_ids: props.user.role_ids });
}
</script>