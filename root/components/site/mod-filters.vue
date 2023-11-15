<template>
    <a-input v-model="filters.query" :label="$t('search')"/>
    <a-game-select v-if="!game" v-model="filters.game_id" :label="$t('game')" :placeholder="$t('any_game')" clearable/>
    <a-category-select
        v-if="categories && categories.data.length"
        v-model="filters.category_id"
        :max-height="500"
        :categories="categories.data"
        :label="$t('category')"
    />
    <a-select v-model="filters.tags" :label="$t('tags')" multiple clearable list-tags color-by="color" :options="tags?.data" max="10" max-shown="2"/>
    <a-select v-model="filters.block_tags" :label="$t('filter_out_tags')" multiple clearable list-tags color-by="color" :options="tags?.data" max="10" max-shown="2"/>
</template>

<script setup lang="ts">
import type { AsyncDataExecuteOptions } from 'nuxt/dist/app/composables/asyncData';
import type { Category, Game, Mod, Tag } from '~~/types/models';
import { Paginator } from '~~/types/paginator';

const props = defineProps<{
    refresh: (opts?: AsyncDataExecuteOptions) => Promise<Paginator<Mod> | null>,
    game?: Game;
    categories?: Paginator<Category>|null;
    refreshCategories: (opts?: AsyncDataExecuteOptions) => Promise<Paginator<Category> | null>;
    filters: {
        query: string;
        game_id: number;
        tags: number[];
        block_tags: number[];
        category_id: number|null;
    };
}>();

const gameId = computed(() => props.game?.id ?? props.filters.game_id);

const { data: tags } = await useFetchMany<Tag>(gameId.value ? `games/${gameId.value}/tags` : 'tags', { 
    params: { type: 'mod', global: true },
    lazy: true
});

watch(() => props.filters.game_id, async () => {
    await props.refresh();

    if (props.filters.game_id) {
        await props.refreshCategories();
    }
});
</script>