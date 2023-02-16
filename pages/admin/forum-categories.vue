<template>
    <a-list url="forum-categories" query :params="{ game_id: gameId, forum_id: !gameId ? 1 : undefined }" :item-link="item => `${pageLink}/${item.id}`" :new-button="`${pageLink}/new`"/>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-forum-categories', props.game);

const gameId = computed(() => props.game?.id);

const pageLink = computed(() => gameId ? `/admin/games/${gameId}/forum-categories` : '/admin/forum-categories');
</script>