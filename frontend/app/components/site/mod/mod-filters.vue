<template>
	<m-input v-model="queryModel" :label="$t('search')"/>
	<game-select v-if="!game" v-model="gameIdModel" :label="$t('game')" :placeholder="$t('any_game')" clearable/>
	<category-select
		v-if="categories && categories.length"
		v-model="categoryIdModel"
		:max-height="400"
		:categories="categories"
		:label="$t('category')"
	/>
	<m-allow-list
		v-if="tags"
		v-model:allow="tagsModel"
		v-model:disallow="blockTagsModel"
		:label="$t('tags')"
		:options="tags?.data"
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
	lazy: true,
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
