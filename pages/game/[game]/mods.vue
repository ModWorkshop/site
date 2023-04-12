<template>
    <page-block :game="game" :breadcrumb="breadcrumb">
        <Title>{{game.name}}</Title>
        <mod-list :game="game" :url="`games/${game.id}/mods`" side-filters/>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';
const store = useStore();
const { t } = useI18n();

definePageMeta({ alias: '/g/:game/mods' });

const { data: game } = await useResource<Game>('game', 'games');

const breadcrumb = computed(() => {
    if (game.value) {
        return [ { name: t('games'), to: 'games' }, { name: game.value.name, to: `g/${game.value.short_name ?? game.value.id}`}, { name: t('mods') } ];
    }
});

watch(() => game.value, () =>  store.currentGame = game.value, { immediate: true });
</script>
