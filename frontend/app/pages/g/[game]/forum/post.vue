<template>
	<edit-thread-page :game="game"/>
</template>

<script lang="ts" setup>
import type { Game } from '~/types/models';

definePageMeta({
	alias: '/game/:game/forum/post',
	middleware: 'unbanned-users-only'
});

const { game } = defineProps<{
	game: Game;
}>();
</script>
