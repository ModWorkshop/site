<template>
	<m-input v-model="queryModel" :label="$t('search')"/>
	<game-select v-if="!game" v-model="gameIdModel" :label="$t('game')" :placeholder="$t('any_game')" clearable/>
	<category-select
		v-if="categories && categories.length"
		v-model="categoryIdModel"
		:max-height="500"
		:categories="categories"
		:label="$t('category')"
	/>
	<m-select
		v-model="tagsModel"
		:label="$t('tags')"
		multiple
		clearable
		list-tags
		color-by="color"
		:options="tags?.data"
		max="10"
		max-shown="1"
	/>
	<m-select
		v-model="blockTagsModel"
		:label="$t('filter_out_tags')"
		multiple
		clearable
		list-tags
		color-by="color"
		:options="tags?.data"
		max="10"
		max-shown="1"
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
}>();

const queryModel = defineModel<string>('query');
const gameIdModel = defineModel<number>('gameId');
const tagsModel = defineModel<number[]>('tags');
const blockTagsModel = defineModel<number[]>('blockTags');
const categoryIdModel = defineModel<number>('categoryId');

const gameId = computed(() => props.game?.id ?? gameIdModel.value);

const { data: tags } = await useFetchMany<Tag>(() => gameId.value ? `games/${gameId.value}/tags` : 'tags', {
	watch: [gameId],
	query: {
		type: 'mod',
		global: true
	}
});

watch(gameIdModel, async () => {
	await props.refresh();

	if (gameIdModel.value) {
		await props.refreshCategories();
	}
});
</script>
