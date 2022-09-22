<template>
    <a-list 
        query 
        :new-button="`/admin/games/${gameId}/instructions-templates/new`"
        :url="`games/${gameId}/instructs-templates`" 
        :item-link="item => `/admin/games/${gameId}/instructions-templates/${item.id}`" 
    />
</template>

<script setup lang="ts">
const route = useRoute();
const gameId = computed(() => route.params.gameId);
</script>