<template>
    <m-list url="forum-categories" query :pagination="false" :params="{ game_id: gameId, forum_id: !gameId ? 1 : undefined }" :item-link="item => `${pageLink}/${item.id}`" :new-button="`${pageLink}/new`"/>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-forum-categories', props.game);

const gameId = computed(() => props.game?.id);

const pageLink = computed(() => getAdminUrl('forum-categories', props.game));
</script>