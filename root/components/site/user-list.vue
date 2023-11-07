<template>
    <flex column>
        <flex :gap="column ? 0 : 3" :class="{'flex-col': true, 'md:flex-row': !column}">
            <content-block grow :class="{'self-auto': true, 'md:self-start': !column}" style="flex: 1;">
                <a-input v-model="query" :label="$t('search')"/>
                <a-select v-model="roleIds" multiple url="roles" :label="$t('roles')"/>
                <a-select v-if="game" v-model="gameRoleIds" multiple :url="`games/${game.id}/roles`" :label="$t('game_roles')"/>
            </content-block>
            <content-block grow style="flex: 4;" gap="1">
                <a-items v-model:page="page" :items="users" :loading="loading">
                    <template #item="{ item }">
                        <NuxtLink :key="item.id" :to="userLink(item)" class="list-button">
                            <a-user :user="item" static/>
                        </NuxtLink>
                    </template>
                </a-items>
            </content-block>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import type { User, Game } from '~~/types/models';

const page = useRouteQuery('page', 1);
const query = useRouteQuery('query', '');
const roleIds = useRouteQuery('role_ids', []);
const gameRoleIds = useRouteQuery('game_role_ids', []);
const searchBus = useEventBus<string>('search');

searchBus.on(search => query.value = search);

const props = withDefaults(defineProps<{
    userLink?: (user: User) => string,
    column?: boolean,
    url?: string,
    game?: Game
}>(), {
    userLink: (user: User) => `/user/${user.id}`,
    url: 'users',
    column: false
});

const { data: users, loading } = await useWatchedFetchMany<User>(() => props.url, { 
    page,
    query,
    role_ids: roleIds,
    game_role_ids: gameRoleIds
});
</script>