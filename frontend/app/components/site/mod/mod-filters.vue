<template>
	<m-input v-model="filters.query" :label="$t('search')"/>
	<game-select v-if="!game" v-model="filters.game_id" :label="$t('game')" :placeholder="$t('any_game')" clearable/>
	<category-select
		v-if="categories && categories.length"
		v-model="filters.category_id"
		:max-height="500"
		:categories="categories"
		:label="$t('category')"
	/>
	<m-select
		v-model="filters.tags"
		:label="$t('tags')"
		multiple
		clearable
		list-tags
		color-by="color"
		:options="tags?.data"
		max="10"
		max-shown="2"
	/>
	<m-select
		v-model="filters.block_tags"
		:label="$t('filter_out_tags')"
		multiple
		clearable
		list-tags
		color-by="color"
		:options="tags?.data"
		max="10"
		max-shown="2"
	/>
</template>

<script setup lang="ts">
import type { AsyncDataExecuteOptions } from '~/types/core';
import type { Category, Game, Tag } from '~/types/models';

const props = defineProps<{
	refresh: (opts?: AsyncDataExecuteOptions) => Promise<void>;
	game?: Game;
	categories?: Category[] | null;
	refreshCategories: (opts?: AsyncDataExecuteOptions) => Promise<void>;
	filters: {
		query: string;
		game_id: number;
		tags: number[];
		block_tags: number[];
		category_id: number | null;
	};
}>();

const gameId = computed(() => props.game?.id ?? props.filters.game_id);

const { data: tags } = await useFetchMany<Tag>(() => gameId.value ? `games/${gameId.value}/tags` : 'tags', {
	watch: [gameId],
	query: {
		type: 'mod',
		global: true
	}
});

watch(() => props.filters.game_id, async () => {
	await props.refresh();

	if (props.filters.game_id) {
		await props.refreshCategories();
	}
});
</script>
