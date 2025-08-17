<template>
    <page-block v-if="game" :game="game" :breadcrumb="breadcrumb" show-background>
        <Title>{{ game.name }} Mods</Title>
        <NuxtPage :game="game" :mods="mods"/>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Breadcrumb, Game, Mod } from '~/types/models';
import { Paginator } from '~/types/paginator';
const store = useStore();
const route = useRoute();
const { t } = useI18n();

definePageMeta({ alias: '/game/:game' });

let game = ref<Game>();
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
    game.value = data.value.game;
    mods = data.value.mods;
} else {
    const { data } = await useResource<Game>('game', 'games');
    game.value = data.value;
}

const desc = `Browse ${shortStat(game.value.mods_count!)} ${game.value.name} mods. Find a big variety of mods to customize ${game.value.name} on ModWorkshop!`;

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

watch(() => game, () => store.currentGame = game.value!, { immediate: true });
</script>