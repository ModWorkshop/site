<template>
	<m-flex column gap="3">
		<mod-list
			v-if="user?.extra?.game_show_mods ?? true"
			:title="$t('mods')"
			:default-sort-by="user?.extra?.game_default_mods_sort"
			:title-link="`/g/${game.short_name}/mods`"
			side-filters
			:initial-mods="mods"
			:categories="game.categories"
			query
			:game="game"
		>
			<template #title>
				<span class="ml-auto">
					{{ $t('mod_count', { n: game.mods_count }) }}
				</span>
			</template>
		</mod-list>
		<div id="mws-ads-mid" class="ad mx-auto"/>
		<div id="mws-ads-mid-mobile" class="ad mx-auto"/>
		<thread-list
			v-if="user?.extra?.game_show_threads ?? true"
			:title="$t('threads')"
			:title-link="`/g/${game.short_name}/forum`"
			:game-id="game?.id"
			:forum-id="game?.forum_id"
			:limit="10"
			no-pins
			:lazy="user?.extra?.game_show_mods ?? true"
			:query="false"
			:filters="false"
		/>
	</m-flex>
</template>
<script setup lang="ts">
import { Paginator } from '~/types/paginator';
import { useStore } from '~/store';
import type { Game, Mod } from '~/types/models';
const store = useStore();
const { user } = store;

definePageMeta({ alias: '/game/:game' });

const { game } = defineProps<{
	game: Game;
	mods?: Paginator<Mod>;
}>();

watch(() => game, () => store.currentGame = game, { immediate: true });
</script>
