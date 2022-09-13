<template>
    <a-list
        url="documents"
        query
        :item-link="item => `${url}/${item.id}`"
        :new-button="`${url}/new`"
        :params="{ game_id: route.params.gameId }"
    />
</template>

<script setup lang="ts">
const route = useRoute();

const url = computed(() => route.params.gameId ? `/admin/games/${route.params.gameId}/docs` : '/admin/docs');
</script>