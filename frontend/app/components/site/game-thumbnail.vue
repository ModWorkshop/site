<template>
	<a-thumbnail :src="compSrc" :has-thumb="false" url-prefix="games/images" :alt="game.name" :title="game.name"/>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

const { src, game } = defineProps<{
	src?: string | Blob;
	game: Game;
}>();

const compSrc = computed(() => src ?? game.thumbnail);
</script>
