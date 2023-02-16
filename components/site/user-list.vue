<template>
    <flex column>
        <flex gap="3">
            <content-block grow class="self-start" style="flex: 1;">
                <a-input v-model="query" :label="$t('search')"/>
            </content-block>
            <content-block grow style="flex: 4;" gap="1">
                <a-items v-model:page="page" :items="users" :loading="loading">
                    <template #item="{ item }">
                        <NuxtLink :key="item.id" :to="`/user/${item.id}`" class="list-button">
                            <a-user :user="item"/>
                        </NuxtLink>
                    </template>
                </a-items>
            </content-block>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

const page = useRouteQuery('page', 0);
const query = useRouteQuery('query', '');

const { data: users, loading } = await useWatchedFetchMany<User>('users', { 
    page,
    query
});
</script>