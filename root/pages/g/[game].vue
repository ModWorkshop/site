<template>
    <page-block :game="game" :breadcrumb="breadcrumb" game-banner>
        <Title>{{ game.name }} Mods</Title>

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

const desc = `
Browse ${game.value.mods_count} mods for ${game.value.name}. Find a big variety of mods to customize ${game.value.name} and enjoy the creativity of the modding community.
ModWorkshop is the platform for sharing and downloading mods for ${game.value.name}. Working together as a community to create tools, guides and more.`;

useServerSeoMeta({
    ogSiteName: `ModWorkshop - ${game.value.name}`,
	ogTitle: `${game.value.name}`,
	description: desc,
	ogDescription:desc,
	ogImage: `games/images/${game.value.thumbnail}`,
    keywords: `${game.value.name}, ${game.value.name} mod, ${game.value.name} mods, mod, mods, modding, modworkshop, modworkshopnet`,
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