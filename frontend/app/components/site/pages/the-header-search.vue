<template>
	<m-dropdown v-model:open="showSearch" :close-on-click="false" :trap-focus="false" :auto-hide="false" align="end" dropdown-class="search-dropdown">
		<m-flex>
			<input
				v-if="showSearch"
				id="header-search"
				ref="searchInput"
				v-model="query"
				type="search"
				class="searchbox"
				inline
				:placeholder="$t('search')"
				@click.stop
				@keydown="onKeydownSearch"
				@keyup.enter="clickSelectedSearch"
			>
			<m-flex v-else id="header-search" inline class="searchbox">
				<i-mdi-magnify/><span class="text-secondary my-auto">{{ $t('search') }}</span>
				<span class="ml-auto my-auto max-md:hidden">
					<kbd>CTRL</kbd> <kbd>K</kbd>
				</span>
			</m-flex>
		</m-flex>
		<template #content>
			<template v-if="query && currentGame">
				<m-flex column>
					<span class="text-secondary m-2">{{ $t('search_results_game', [currentGame.name]) }}</span>

					<m-flex v-if="currentGame && (loadingGameMods || (gameMods?.data?.length))" column gap="2" class="w-full px-2">
						<m-loading v-if="loadingGameMods"/>
						<template v-else-if="gameMods">
							<search-list-mod v-for="mod of gameMods.data" :key="mod.id" :mod="mod" :show-game="false" @click="showSearch = false"/>
						</template>
						<m-button color="subtle" :to="`/g/${currentGame.short_name}?query=${query}`"><i-mdi-magnify/> {{ $t('search_view_all') }}</m-button>
					</m-flex>
					<h4 v-else-if="gameMods?.data?.length == 0" class="px-2 text-center">
						{{ $t('no_mods_found') }}
					</h4>
				</m-flex>

				<div class="dropdown-splitter"/>
			</template>

			<template v-if="query">
				<m-flex column>
					<span class="text-secondary m-2">{{ $t('search_results') }}</span>

					<m-flex v-if="loadingMods || globalMods?.data?.length" column gap="2" class="w-full px-2">
						<m-loading v-if="loadingMods"/>
						<template v-else-if="globalMods">
							<search-list-mod v-for="mod of globalMods.data" :key="mod.id" :mod="mod" @click="showSearch = false"/>
						</template>
						<m-button color="subtle" :to="`/search/mods?query=${query}`"><i-mdi-magnify/> {{ $t('search_view_all') }}</m-button>
					</m-flex>
					<h4 v-else-if="globalMods?.data?.length == 0" class="px-2 text-center">
						{{ $t('no_mods_found') }}
					</h4>
				</m-flex>

				<div class="dropdown-splitter"/>
			</template>

			<template v-if="currentGame">
				<m-flex v-if="gameButtons" column>
					<span class="text-secondary m-2">{{ $t('search_in_game', [currentGame.name]) }}</span>
					 <m-flex column>
						<m-dropdown-item
							v-for="[, button] in gameButtons.entries()"
							:key="button.text"
								:icon="button.icon"
								:to="`${button.to}?query=${query}`"
								color="subtle"
							>
								{{ $t(button.text) }}
						</m-dropdown-item>
					 </m-flex>
				 </m-flex>

				<div class="dropdown-splitter"/>
			</template>

			<m-flex column>
				<span class="text-secondary m-2">{{ $t('search_globally') }}</span>

				<m-dropdown-item
					v-for="[, button] in searchButtons.entries()"
					:key="button.text"
					:icon="button.icon"
					:to="`${button.to}?query=${query}`"
				>
					{{ $t(button.text) }}
				</m-dropdown-item>
			</m-flex>
		</template>
	</m-dropdown>
</template>
<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useStore } from '~/store';
import type { Mod } from '~/types/models';

import IMdiPuzzle from '~icons/mdi/puzzle';
import IMdiController from '~icons/mdi/controller';
import IMdiForum from '~icons/mdi/forum';
import IMdiUser from '~icons/mdi/user';

const store = useStore();
const router = useRouter();
const query = ref('');
const debouncedQuery = refDebounced(query);
const showSearch = ref(false);

const { currentGame } = storeToRefs(store);
const searchInput = ref();

watch(searchInput, val => {
	if (val) {
		setTimeout(() => val.focus(), 100);
	}
});

const searchButtons = [
	{ to: `/search/mods`, text: 'mods', icon: IMdiPuzzle },
	{ to: `/search/threads`, text: 'threads', icon: IMdiForum },
	{ to: `/search/users`, text: 'users', icon: IMdiUser },
	{ to: `/search/games`, text: 'games', icon: IMdiController }
];

const gameButtons = computed(() => {
	if (currentGame.value) {
		return [
			{ to: `/g/${currentGame.value.short_name}/mods`, text: 'mods', icon: IMdiPuzzle },
			{ to: `/g/${currentGame.value.short_name}/forum`, text: 'threads', icon: IMdiForum }
		];
	}
});

// Simple mod searcher
const { data: globalMods, refresh: refreshGlobal, loading: loadingMods } = await useFetchMany<Mod>('mods', {
	query: {
		limit: 4,
		sort: 'best_match',
		query: debouncedQuery,
		exclude_game_ids: computed(() => currentGame.value ? [currentGame.value?.id] : undefined)
	},
	watch: [],
	immediate: false
});

const { data: gameMods, refresh: refreshGame, loading: loadingGameMods } = await useFetchMany<Mod>(() => currentGame.value ? `games/${currentGame.value.id}/mods` : '', {
	query: {
		limit: 4,
		sort: 'best_match',
		query: debouncedQuery
	},
	watch: [],
	immediate: false
});

watch([debouncedQuery, currentGame, showSearch], val => {
	if (showSearch.value && val.length) {
		refreshGlobal();

		if (currentGame.value) {
			refreshGame();
		}
	}
});

onMounted(() => {
	window.addEventListener('keydown', function (e) {
		if (e.ctrlKey && e.key === 'k' /** k */) {
			showSearch.value = true;
			e.preventDefault();
		}

		if (e.key === 'Escape') {
			showSearch.value = false;
		}
	});
});

function onKeydownSearch() {
	if (query.value) {
		showSearch.value = true;
	}
}

function clickSelectedSearch() {
	router.replace({ path: '/search/mods', query: { query: query.value } });
}

</script>

<style>
.selected-search {
	background-color: var(--primary-color);
}

kbd {
	vertical-align: middle;
}

#header-search {
	line-height: revert;
	width: 280px;
	height: 36px;
}

@media (max-width: 1024px) {
	#header-search {
		width: 200px;
	}
}

.search-buttons {
	display: flex;
	flex-wrap: wrap;
	width: 100%;
	gap: 0.25rem;
}

.search-dropdown {
	width: 500px;
	max-width: 500px;
	max-height: calc(100vh - 64px);
	padding: 1rem;
}

.searchbox:focus-visible {
	outline: none;
	border-color: var(--primary-color);
}

.searchbox {
	padding: 0.7rem;
	flex: 1;
	transition: border-color 0.25s;
	color: var(--text-color);
	background-color: var(--input-bg-color);
	border-radius: var(--border-radius);
}

@media (max-width: 767px) {
	.search-dropdown {
		max-width: calc(100vw - 8px);
		max-height: calc(100vh - 250px);
	}
}
</style>
