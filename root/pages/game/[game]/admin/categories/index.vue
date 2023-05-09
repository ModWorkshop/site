<template>
    <flex column>
        <category-tree :categories="categories?.data">
            <template #buttons>
                <a-button class="ml-auto" to="categories/new">{{$t('new')}}</a-button>
            </template>
            <template #button="{category}">
                <a-button class="ml-auto" icon="mdi:cog" :to="`${gameUrl}/${category.id}`">{{$t('edit')}}</a-button>
            </template>
        </category-tree>
    </flex>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-categories', props.game);

const route = useRoute();

const url = computed(() => `games/${route.params.game}/categories`);
const gameUrl = computed(() => getAdminUrl('categories', props.game));

const { data: categories } = await useFetchMany(() => url.value);
</script>