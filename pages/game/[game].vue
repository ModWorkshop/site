<template>
    <page-block :game="game" :breadcrumb="breadcrumb" :game-banner="$route.name == 'game-home'">
        <Title>{{game.name}}</Title>
        <div>
            <NuxtPage :game="game"/>
        </div>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';
const store = useStore();
const route = useRoute();
const { t } = useI18n();

definePageMeta({
    alias: '/g/:game',
});

const { data: game } = await useResource<Game>('game', 'games');

const breadcrumb = computed(() => {
    if (game.value) {
        return [
            { name: t('games'), to: 'games' },
            { name: game.value.name },
        ];
    }
});

watch(() => route.path, () => {
    store.currentGame = game.value;
}, { immediate: true });


// const { data: lastThreads } = await useFetchMany<Thread>(`threads?forum_id=${game.value.forum.id}`);
</script>