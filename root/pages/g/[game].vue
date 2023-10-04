<template>
    <page-block :game="game" :breadcrumb="breadcrumb" game-banner>
        <Title>{{ game.name }} Mods</Title>
        <NuxtPage :game="game" :mods="mods"/>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, Game } from '~~/types/models';
import { Paginator } from '../../types/paginator';
import { Mod } from '../../types/models';
const store = useStore();
const route = useRoute();
const { t } = useI18n();

definePageMeta({ alias: '/game/:game' });

let game: Game;
let mods: Paginator<Mod>;

if (route.name == 'g-game') {
    const { data } = await useResource<{ game: Game, mods: Paginator<Mod> }>('game', id => `games/${id}/game-section-data`, undefined, {
        page: route.query.page,
        query: route.query.query,
        'fields[mods]': listModFields.join(','),
        category_id: route.query.category,
        tags: useRouteQuery('selected-tags', []).value,
        block_tags: useRouteQuery('filtered-tags', []).value,
        sort: useRouteQuery('sort', 'bumped_at').value,
        limit: 20
    });
    game = data.value.game;
    mods = data.value.mods;
} else {
    const { data } = await useResource<Game>('game', 'games');
    game = data.value;
}

const desc = `Browse ${shortStat(game.mods_count!)} ${game.name} mods. Find a big variety of mods to customize ${game.name} on ModWorkshop!`;

useServerSeoMeta({
    ogSiteName: `ModWorkshop - ${game.name}`,
	ogTitle: `${game.name}`,
	description: desc,
	ogDescription:desc,
	ogImage: `games/images/${game.thumbnail}`,
    keywords: `${game.name}, ${game.name} mod, ${game.name} mods, mod, mods, modding, modworkshop, modworkshopnet`,
	twitterCard: 'summary',
});

const breadcrumb = computed(() => {
    const breadcrumb: Breadcrumb[] = [];
    if (game) {
        breadcrumb.push({ name: t('games'), to: 'games' });
        breadcrumb.push({ name: game.name, to: `g/${game.short_name || game.id}` });
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

watch(() => game, () =>  store.currentGame = game, { immediate: true });
</script>