<template>
    <div>
        <a-list v-model="reports" :url="url" query :params="{ all }">
            <template #item="{ item }">
                <admin-report :report="item" :reports="reports.data" :game="game"/>
            </template>
        </a-list>
    </div>
</template>

<script setup lang="ts">
import { Game, Report, } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const url = computed(() => getGameResourceUrl('reports', props.game));
const all = useRouteQuery('all', true, 'boolean');

const reports = ref<Paginator<Report>>({ data: [], meta: null });
</script>

<style>
.archived {
    opacity: 0.25;
}
</style>