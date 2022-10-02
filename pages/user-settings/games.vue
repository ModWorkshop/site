<template>
    <flex column>
        <a-async-select v-model="selectedGame" url="games"/>
        <flex style="width: 350px;">
            <content-block alt-background gap="1" padding="5" grow>
                <span class="text-xl">
                    <a-user :user="user" avatar-size="lg"/>
                </span>
                <a-tag-selector 
                    v-model="user.role_ids"
                    :label="$t('roles')"
                    :options="roles?.data"
                    :option-enabled="role => role.assignable"
                />
                <a-tag-selector 
                    v-if="gameUserData"
                    v-model="gameUserData.role_ids"
                    :label="$t('game_roles')"
                    :options="gameRoles?.data" 
                    :option-enabled="role => role.assignable"
                    @update:model-value="saveGameRoles"
                />
            </content-block>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { GameUserData, Role, UserForm } from '~~/types/models';

const selectedGame = useRouteQuery('game', undefined, 'number');
const props = defineProps<{
    user: UserForm
}>();

const { user: me } = useStore();

const { data: gameRoles, refresh } = await useFetchMany<Role>(() => `games/${selectedGame.value}/roles`, { immediate: !!selectedGame.value });
const { data: roles } = await useFetchMany<Role>('roles', {  params: { only_assignable: 1 } });
const { data: gameUserData, refresh: loadGameData } = await useFetchData<GameUserData>(() => `games/${selectedGame.value}/users/${props.user.id}`, { immediate: !!selectedGame.value });

watch(selectedGame, () => {
    if (selectedGame.value) {
        refresh();
        loadGameData();
    }
});

async function saveGameRoles() {
    await usePatch(`games/${selectedGame.value}/users/${props.user.id}`, { role_ids: gameUserData.value.role_ids });
}
</script>