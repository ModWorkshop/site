<template>
	<NuxtLink class="game content-block" :to="`/g/${game.short_name}`">
		<m-card center class="h-full">
			<template #thumbnail>
				<a-thumbnail url-prefix="games/images" :lazy="lazyThumbnails" :src="game.thumbnail" :title="game.name" :alt="game.name"/>
			</template>
			<span v-if="game.mods_count || game.mods_count === 0" class="text-secondary">{{ $t('mod_count', { n: game.mods_count }) }}</span>
		</m-card>
	</NuxtLink>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

defineProps<{
	game: Game;
	lazyThumbnails?: boolean;
}>();
</script>
