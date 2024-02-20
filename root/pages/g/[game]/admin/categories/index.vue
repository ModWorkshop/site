<template>
    <m-flex column>
        <category-tree :categories="categories?.data">
            <template #buttons>
                <m-button class="ml-auto" to="categories/new">{{$t('new')}}</m-button>
            </template>
            <template #button="{category}">
                <m-button class="ml-auto" :to="`${gameUrl}/${category.id}`"><i-mdi-cog/> {{$t('edit')}}</m-button>
            </template>
        </category-tree>
    </m-flex>
</template>

<script setup lang="ts">
import type { Category, Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-categories', props.game);

const route = useRoute();

const url = computed(() => `games/${route.params.game}/categories`);
const gameUrl = computed(() => getAdminUrl('categories', props.game));

const { data: categories } = await useFetchMany<Category>(() => url.value);
</script>