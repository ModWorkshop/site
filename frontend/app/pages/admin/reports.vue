<template>
    <div>
        <m-list v-model:page="page" query :items="reports" :loading="loading">
            <template #item="{ item }">
                <admin-report :report="item" :reports="reports!.data" :game="game"/>
            </template>
        </m-list>
    </div>
</template>

<script setup lang="ts">
import type { Game, Report } from '~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const url = computed(() => getGameResourceUrl('reports', props.game));
const all = useRouteQuery('all', true, 'boolean');
const page = ref(1);

const { data: reports, loading } = await useWatchedFetchMany<Report>(url.value, { page, all: all.value });
</script>

<style>
.archived {
    opacity: 0.25;
}
</style>