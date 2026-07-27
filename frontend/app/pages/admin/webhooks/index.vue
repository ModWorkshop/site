<template>
	<m-list
		:url="ApiUrl"
		query
		:item-link="item => `${url}/${item.id}`"
		:new-button="`${url}/new`"
		:params="{ game_id: game?.id, global: !game }"
	/>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

const props = defineProps<{
	game: Game;
}>();

useNeedsPermission('manage-webhooks', props.game);

const url = computed(() => getAdminUrl('webhooks', props.game));
const ApiUrl = computed(() => getGameResourceUrl('webhooks', props.game));
</script>
