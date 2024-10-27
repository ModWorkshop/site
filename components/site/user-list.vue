<template>
    <m-flex column>
        <m-flex :gap="column ? 0 : 3" :class="{'flex-col': true, 'md:flex-row': !column}">
            <m-content-block grow :class="{'self-auto': true, 'md:self-start': !column}" style="flex: 1;">
                <m-input v-model="query" :label="$t('search')"/>
                <m-select v-model="roleIds" multiple url="roles" :label="$t('roles')"/>
                <m-select v-if="game" v-model="gameRoleIds" multiple :url="`games/${game.id}/roles`" :label="$t('game_roles')"/>
            </m-content-block>
            <m-flex grow style="flex: 4;" gap="1">
                <m-list v-model:page="page" query :items="users" :loading="loading">
                    <template #item="{ item }">
                        <m-content-block :key="item.id" class="cursor-pointer p-4" @click="() => $router.push(getUserLink(item))">
                            <a-user :user="item"/>
                        </m-content-block>
                    </template>
                </m-list>
            </m-flex>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import type { User, Game } from '~~/types/models';

const page = ref(1);
const query = useRouteQuery('query', '');
const roleIds = useRouteQuery('role_ids', []);
const gameRoleIds = useRouteQuery('game_role_ids', []);
const searchBus = useEventBus<string>('search');

searchBus.on(search => query.value = search);

const { url = 'users', column = false, userLink } = defineProps<{
    userLink?: (user: User) => string,
    column?: boolean,
    url?: string,
    game?: Game
}>();

function getUserLink(user) {
    if (typeof userLink == 'function') {
        return userLink(user);
    } else {
        return `/user/${user.id}`;
    }
}

const { data: users, loading } = await useWatchedFetchMany<User>(() => url, { 
    page,
    query,
    role_ids: roleIds,
    game_role_ids: gameRoleIds
});
</script>