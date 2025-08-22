<template>
	<m-list :url="url" query limit="25">
		<template #item="{ item, items }">
			<admin-audit-log :log="item" :logs="items.data"/>
		</template>
	</m-list>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

useNeedsPermission('can-see-audit-log');

const { game } = defineProps<{
	game?: Game;
}>();

const url = computed(() => getGameResourceUrl('audit-logs', game));
</script>
