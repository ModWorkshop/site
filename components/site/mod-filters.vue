<template>
    <a-input v-model="filters.query" :label="$t('search')"/>
    <a-game-select v-if="!forcedGame" v-model="filters.game_id" :label="$t('game')" :placeholder="$t('any_game')" clearable/>
    <a-category-select 
        v-if="categories && categories.data.length"
        set-query
        :categories="categories.data"
        :label="$t('categories')"
    />
    <a-select v-model="filters.tags" :label="$t('tags')" multiple clearable :options="tags.data" max="10" max-shown="2"/>
    <a-select v-model="filters.block_tags" :label="$t('filter_out_tags')" multiple clearable :options="tags.data" max="10" max-shown="2"/>
</template>

<script setup lang="ts">
import { AsyncDataExecuteOptions } from 'nuxt/dist/app/composables/asyncData';
import { Category, Tag } from '~~/types/models';

const props = defineProps<{
    refresh: (opts?: AsyncDataExecuteOptions) => Promise<void>,
    forcedGame?: number;
    filters: {
        query: string;
        game_id: number;
        tags: number[];
        block_tags: number[];
    };
}>();

const selectedCategory = useRouteQuery('category');

const { data: tags } = await useFetchMany<Tag>('tags', { params: { type: 'mod' }, initialCache: true });

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${props.filters.game_id}/categories?include_paths=1`, { 
    immediate: !!props.filters.game_id
});

watch(() => props.filters.game_id, async () => {
    console.log('..');
    
    selectedCategory.value = null;

    await props.refresh();

    if (props.filters.game_id) {
        await refetchCats();
    }
});
</script>