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
			<m-flex column gap="4" class="p-2 items-center">
				<m-flex v-if="gameButtons" class="w-full" column gap="2">
					<h3>{{ $t('game_search') }}</h3>
					<div class="search-buttons">
						<m-button
							v-for="[, button] in gameButtons.entries()"
							:key="button.text"
							:to="`${button.to}?query=${query}`"
							color="subtle"
						>
							{{ $t(button.text) }}
						</m-button>
					</div>
				</m-flex>

				<m-flex class="w-full" column gap="2">
					<h3>{{ $t('search') }}</h3>
					<div class="search-buttons">
						<m-button
							v-for="[, button] in searchButtons.entries()"
							:key="button.text"
							:to="`${button.to}?query=${query}`"
							color="subtle"
						>
							{{ $t(button.text) }}
						</m-button>
					</div>
				</m-flex>
				<span v-if="!query">
					{{ $t('search_start_searching') }}
				</span>
				<template v-else-if="gameMods?.data?.length || globalMods?.data?.length">
					<m-flex v-if="currentGame && gameMods && gameMods.data?.length" column gap="2">
						<h3>{{ $t('search_mods_game', [currentGame.name]) }}</h3>
						<m-loading v-if="loadingGameMods"/>
						<template v-else-if="gameMods">
							<search-list-mod v-for="mod of gameMods.data" :key="mod.id" lite :mod="mod" @click="showSearch = false"/>
						</template>
						<m-button color="subtle" :to="`/g/${currentGame.short_name}?query=${query}`">{{ $t('search_view_all') }}</m-button>
					</m-flex>
					<m-flex v-if="globalMods && globalMods.data?.length" column gap="2">
						<h3>{{ $t(currentGame ? 'search_mods_other_games' : 'mods') }}</h3>
						<m-loading v-if="loadingMods"/>
						<template v-else-if="globalMods">
							<search-list-mod v-for="mod of globalMods.data" :key="mod.id" lite :mod="mod" @click="showSearch = false"/>
						</template>
						<m-button color="subtle" :to="`/search/mods?query=${query}`">{{ $t('search_view_all') }}</m-button>
					</m-flex>
				</template>
				<span v-else>
					{{ $t('nothing_found') }}
				</span>
			</m-flex>
		</template>
	</m-dropdown>
</template>
<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useStore } from '~/store';
import type { Mod } from '~/types/models';

const store = useStore();
const router = useRouter();
const query = ref('');
const debouncedQuery = refDebounced(query);
const startedModsSearch = ref(false);
const startedGameModsSearch = ref(false);
const showSearch = ref(false);

const { currentGame } = storeToRefs(store);
const searchInput = ref();

watch(searchInput, val => {
	if (val) {
		setTimeout(() => val.focus(), 100);
	}
});

watch(debouncedQuery, val => {
	if (val.length) {
		if (!startedModsSearch.value) {
			refreshGlobal();
			startedModsSearch.value = true;
		}
		if (!startedGameModsSearch.value) {
			refreshGame();
			startedGameModsSearch.value = true;
		}
	}
});

const searchButtons = [
	{ to: `/search/mods`, text: 'mods' },
	{ to: `/search/threads`, text: 'threads' },
	{ to: `/search/users`, text: 'users' },
	{ to: `/search/games`, text: 'games' }
];

const gameButtons = computed(() => {
	if (currentGame.value) {
		return [
			{ to: `/g/${currentGame.value.short_name}/mods`, text: 'mods' },
			{ to: `/g/${currentGame.value.short_name}/forum`, text: 'threads' }
		];
	}
});

// Simple mod searcher
const { data: globalMods, refresh: refreshGlobal, loading: loadingMods } = await useFetchMany<Mod>('mods', {
	query: {
		limit: 5,
		sort: 'best_match',
		query: debouncedQuery,
		exclude_game_ids: computed(() => currentGame.value ? [currentGame.value?.id] : undefined)
	},
	watch: [debouncedQuery, currentGame],
	immediate: false
});

const { data: gameMods, refresh: refreshGame, loading: loadingGameMods } = await useFetchMany<Mod>(() => currentGame.value ? `games/${currentGame.value.id}/mods` : '', {
	query: {
		limit: 5,
		sort: 'best_match',
		query: debouncedQuery
	},
	watch: [debouncedQuery, currentGame],
	immediate: false
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
	display: grid;
	width: 100%;
	gap: 0.25rem;
	grid-template-columns: repeat(auto-fill, minmax(45%, 1fr));
}

.search-dropdown {
	width: 500px;
	max-width: 500px;
	max-height: 72vh;
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
		max-width: 360px;
	}
}
</style>
