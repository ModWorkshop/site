<template>
    <page-block :game="game" :breadcrumb="breadcrumb" game-banner>
        <Title>{{game.name}}</Title>
        <NuxtPage :game="game"/>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, Game } from '~~/types/models';
const store = useStore();
const route = useRoute();
const { t } = useI18n();

definePageMeta({ alias: '/game/:game' });

const { data: game } = await useResource<Game>('game', 'games');

const desc = `Explore ${game.value.name} mods, discussions & more on ModWorkshop!`;

useServerSeoMeta({
    ogSiteName: `ModWorkshop - ${game.value.name} - Mod`,
	ogTitle: `${game.value.name}`,
	description: desc,
	ogDescription:desc,
	ogImage: `games/images/${game.value.thumbnail}`,
	twitterCard: 'summary',
});

const breadcrumb = computed(() => {
    const breadcrumb: Breadcrumb[] = [];
    if (game.value) {
        breadcrumb.push({ name: t('games'), to: 'games' });
        breadcrumb.push({ name: game.value.name, to: `g/${game.value.short_name || game.value.id}` });
    }

    if (route.name == 'game-game-upload') {
        breadcrumb.push({ name: t('upload_mod') });
    } else if (route.name?.toString().startsWith('game-game-forum')) {
        breadcrumb.push({ name: t('forum'), attachToPrev: 'forum' });
    } else if (route.name == 'game-game-mods') {
        breadcrumb.push({ name: t('mods') });
    } else if (route.name == 'game-game-user-user') {
        breadcrumb.push({ name: t('game_preferences') });
    } else {
        return undefined;
    }

    if (route.name == 'game-game-forum-post') {
        breadcrumb.push({ name: t('post') });
    }

    return breadcrumb;
});

watch(() => game.value, () =>  store.currentGame = game.value, { immediate: true });
</script>