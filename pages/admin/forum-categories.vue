<template>
    <a-list url="forum-categories" :params="{ game_id: gameId, forum_id: !gameId ? 1 : undefined }" :item-link="item => `${pageLink}/${item.id}`" :new-button="`${pageLink}/new`"/>
</template>

<script setup>
const route = useRoute();

const gameId = route.params.gameId;

const pageLink = computed(() => gameId ? `/admin/games/${gameId}/forum-categories` : '/admin/forum-categories');
</script>