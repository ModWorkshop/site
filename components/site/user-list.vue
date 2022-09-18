<template>
    <flex column>
        <a-pagination v-if="!isLoading" v-model="page" :total="users.meta.total" :per-page="users.meta.per_page"/>
        <flex gap="3">
            <content-block grow style="flex: 4;" gap="1">
                <a-loading v-if="isLoading"/>
                <template v-else>
                    <NuxtLink v-for="user of users.data" :key="user.id" :to="`user/${user.id}`" class="list-button">
                        <a-user :user="user"/>
                    </NuxtLink>
                </template>
            </content-block>
            <content-block grow class="self-start" style="flex: 1;">
                <a-input v-model="query" :label="$t('search')"/>
            </content-block>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

const page = useRouteQuery('page', 0);
const query = useRouteQuery('query', '');
const isLoading = ref(true);

const params = reactive({
    page,
    query
});

const { data: users, refresh } = await useFetchMany<User>('users', { 
    params
});

watchThrottled(params, () => refresh(), { throttle: 250 });

isLoading.value = false;
</script>