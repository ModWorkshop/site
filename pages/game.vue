<template>
    <page-block :game="game" :breadcrumb="breadcrumb" game-banner>
        <Title>{{game.name}}</Title>
        <mod-list :forced-game="game.id" :url="`games/${game.id}/mods`"/>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';
const store = useStore();
const { t } = useI18n();

const { data: game } = await useResource<Game>('game', 'games');

const breadcrumb = computed(() => {
    if (game.value) {
        return [
            { name: t('games'), to: 'games' },
            { name: game.value.name },
        ];
    }
});

store.currentGame = game.value;

// const { data: lastThreads } = await useFetchMany<Thread>(`threads?forum_id=${game.value.forum.id}`);
</script>