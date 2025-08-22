<template>
	<user-list
		:url="`games/${game.short_name}/users`"
		:user-link="item => `/g/${game.short_name}/user/${item.id}`"
		column
		alt-background
		:game="game"
	/>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

const props = defineProps<{
	game: Game;
}>();

useNeedsPermission('manage-users', props.game);
</script>
