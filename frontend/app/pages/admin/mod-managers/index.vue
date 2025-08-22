<template>
	<m-list
		:url="apiLink"
		query
		:item-link="item => `${pageLink}/${item.id}`"
		:new-button="`${pageLink}/new`"
		:params="{ global: !game, show_hidden: true }"
	/>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

const props = defineProps<{
	game: Game;
}>();

useNeedsPermission('manage-mods', props.game);

const pageLink = computed(() => getAdminUrl('mod-managers', props.game));
const apiLink = computed(() => getGameResourceUrl('mod-managers', props.game));
</script>
