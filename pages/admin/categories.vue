<template>
    <a-list :url="url" new-button="/admin/categories/new">
        <template #items="{ items }">
            <category-tree :categories="items.data">
                <template #button="{category}">
                    <a-button class="ml-auto" icon="cog" :to="`${gameUrl}/${category.id}`">Edit</a-button>
                </template>
            </category-tree>
        </template>
    </a-list>
</template>

<script setup>
const route = useRoute();

const url = computed(() => `games/${route.params.gameId}/categories`);
const gameUrl = computed(() => `/admin/games/${route.params.gameId}/categories`);
</script>