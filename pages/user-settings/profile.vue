<template>
    <flex gap="2" column>
        <img-uploader v-model="user.avatar_file" label="Avatar" :src="user.avatar">
            <template #label="{ src }">
                <a-avatar size="xl" :src="src"/>
                <a-avatar size="lg" :src="src"/>
                <a-avatar size="md" :src="src"/>
            </template>
        </img-uploader>

        <img-uploader v-model="user.banner_file" label="Banner" :src="user.banner">
            <template #label="{ src }">
                <a-banner :src="src" url-prefix="users/banners"/>
            </template>
        </img-uploader>
        <a-input v-model="user.private_profile" label="Private Profile" type="checkbox" desc="Ticking this on will privatize your profile. Only staff members will be able to view it."/>
        <a-input v-model="user.custom_title" label="Custom Title"/>
        <a-input v-model="user.custom_color" label="Custom Color" type="color"/>
        <a-input :label="$t('roles')">
            <flex gap="3" class="p-4 input-bg" column>
                <a-tag-selector
                    v-model="roleIds"
                    :label="$t('global')"
                    :options="roles?.data"
                    :disabled="user.id !== me.id && !hasPermission('manage-roles')"
                    :option-enabled="role => role.assignable"
                    @update:model-value="prepareSaveRoles"
                />
                <a-select v-model="selectedGame" :label="$t('games')" url="games" clearable/>
                <a-tag-selector 
                    v-if="selectedGame"
                    v-model="gameUserData.role_ids"
                    :disabled="user.id !== me.id && !hasPermission('manage-roles', selectedGame)"
                    :options="gameRoles?.data" 
                    :option-enabled="role => role.assignable"
                    @update:model-value="prepareSaveGameRoles"
                />
            </flex>
        </a-input>
        <md-editor v-model="user.bio" rows="12" label="Bio" desc="Tell about yourself to people visiting your profile"/>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { GameUserData, Role, UserForm } from '~~/types/models';
import clone from 'rfdc/default';

const props = defineProps<{
    user: UserForm
}>();

const { user: me, hasPermission } = useStore();
const selectedGame = useRouteQuery('game', undefined, 'number');

const roleIds = ref(clone(props.user.role_ids));

const { data: roles } = await useFetchMany<Role>('roles', { params: { only_assignable: 1 }, initialCache: true });

const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });

const { data: gameRoles, refresh } = await useFetchMany<Role>(() => `games/${selectedGame.value}/roles`, { immediate: !!selectedGame.value });
const { data: gameUserData, refresh: loadGameData } = await useFetchData<GameUserData>(() => `games/${selectedGame.value}/users/${props.user.id}`, { 
    immediate: !!selectedGame.value
});

watch(selectedGame, () => {
    if (selectedGame.value) {
        refresh();
        loadGameData();
    }
});

async function saveGameRoles() {
    await usePatch(`games/${selectedGame.value}/users/${props.user.id}/roles`, { role_ids: gameUserData.value.role_ids });
}

async function saveRoles() {
    await usePatch(`users/${props.user.id}/roles`, { role_ids: roleIds.value });
}
</script>

<style scoped>

</style>