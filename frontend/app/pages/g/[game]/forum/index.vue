<template>
	<forum-page :game="game"/>
</template>

<script lang="ts" setup>
import type { Game } from '~/types/models';

definePageMeta({ alias: '/game/:game/forum' });

const { game } = defineProps<{
	game: Game;
}>();
</script>
