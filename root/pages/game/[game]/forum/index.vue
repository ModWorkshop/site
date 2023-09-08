<template>
	<forum-page :game="game"/>
</template>

<script lang="ts" setup>
import { Game } from '~/types/models';

definePageMeta({ alias: '/g/:game/forum' });

const { game } = defineProps<{
    game: Game
}>();
</script>