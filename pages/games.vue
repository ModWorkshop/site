<template>
    <page-block>
        <Title>{{$t('games')}}</Title>
        <flex column class="items-center">
            <h2>{{$t('games_hosted', { n: games?.meta.total })}}</h2>
            <i18n-t keypath="want_your_game" tag="h3">
                <template #here>
                    <NuxtLink :to="`forum?category=${settings?.game_requests_forum_category}`">{{$t('here')}}</NuxtLink>
                </template>
            </i18n-t>
        </flex>
        <a-pagination v-model="page" per-page="50" set-query :total="games?.meta.total"/>
        <flex v-if="games" class="games-grid gap-2">
            <a-game v-for="game of games.data" :key="game.id" :game="game"/>
        </flex>
    </page-block>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';
import { useStore } from '../store/index';

const { settings } = useStore();

const page = ref(1);
const { data: games, refresh } = await useFetchMany<Game>('games', { params: reactive({ page }) });

watch(page, async () => await refresh());

</script>