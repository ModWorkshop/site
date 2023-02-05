<template>
    <a-list :url="url" new-button="categories/new" query>
        <template #items="{ items }">
            <category-tree :categories="items.data">
                <template #button="{category}">
                    <a-button class="ml-auto" icon="mdi:cog" :to="`${gameUrl}/${category.id}`">{{$t('edit')}}</a-button>
                </template>
            </category-tree>
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-categories', props.game);

const route = useRoute();

const url = computed(() => `games/${route.params.gameId}/categories`);
const gameUrl = computed(() => `/admin/games/${route.params.gameId}/categories`);
</script>