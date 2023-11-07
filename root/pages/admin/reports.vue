<template>
    <div>
        <a-items v-model:page="page" :items="reports" :loading="loading">
            <template #item="{ item }">
                <admin-report :report="item" :reports="reports!.data" :game="game"/>
            </template>
        </a-items>
    </div>
</template>

<script setup lang="ts">
import type { Game, Report } from '~~/types/models';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const url = computed(() => getGameResourceUrl('reports', props.game));
const all = useRouteQuery('all', true, 'boolean');
const page = useRouteQuery('page', 1, 'number');

const { data: reports, loading } = await useWatchedFetchMany<Report>(url.value, { page, all: all.value });
</script>

<style>
.archived {
    opacity: 0.25;
}
</style>