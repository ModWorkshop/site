<template>
	<page-block>
		<Title>{{ $t('games') }}</Title>
		<m-flex column class="items-center">
			<h2>{{ $t('games_hosted', { n: storeGames?.length }) }}</h2>
			<i18n-t keypath="want_your_game" tag="h3" scope="global">
				<template #here>
					<NuxtLink :to="`forum?category=${settings?.game_requests_forum_category}`">{{ $t('here') }}</NuxtLink>
				</template>
			</i18n-t>
		</m-flex>
		<m-flex :gap="3">
			<m-content-block grow class="self-start" style="flex: 1;" padding="4">
				<m-input v-model="query" :label="$t('search')"/>
			</m-content-block>
			<m-flex grow column style="flex: 4;" gap="1">
				<m-pagination v-model="page" per-page="50" set-query :total="games?.meta.total"/>
				<m-flex v-if="games" class="gap-2 games-grid" wrap>
					<grid-game v-for="game of games.data" :key="game.id" :game="game"/>
				</m-flex>
			</m-flex>
		</m-flex>
	</page-block>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';
import { useStore } from '../store/index';

const { settings, games: storeGames } = useStore();

const page = ref(1);
const query = ref('');

const { data: games, refresh } = await useWatchedFetchMany<Game>('games', { page, query, including_ignored: true });

watch(page, async () => await refresh());

</script>
