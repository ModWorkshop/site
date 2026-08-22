<template>
	<thread-list
		:title="$t('tickets')"
		title-link="/forum"
		:forum-id="game?.forum_id ?? 1"
		alt-background
		:limit="10"
		no-pins
		:query="false"
		:filters="false"
	/>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

const props = defineProps<{
	game?: Game;
}>();

useNeedsPermission('moderate-users', props.game);

</script>
