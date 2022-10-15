<template>
    <page-block>
        <Title>{{$t('games')}}</Title>
        <flex column class="items-center">
            <h2>{{$t('games_hosted')}}</h2>
            <i18n-t keypath="want_your_game" tag="h3">
                <template #here>
                    <a>{{$t('here')}}</a>
                </template>
            </i18n-t>
        </flex>
        <a-pagination v-model="page" per-page="50" set-query :total="games.meta.total"/>
        <content-block class="games-grid gap-2">
            <NuxtLink v-for="game of games.data" :key="game.id" class="flex gap-2 flex-col p-1" :to="`/g/${game.short_name}`">
                <a-thumbnail class="ratio-image round" url-prefix="games/thumbnails" :src="game.thumbnail"/>
                <flex class="text-lg">
                    <span>{{game.name}}</span>
                    <span class="text-secondary ml-auto">{{$t('mods_count', { n: game.mods_count })}}</span>
                </flex>
            </NuxtLink>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const page = ref(1);
const { data: games, refresh } = await useFetchMany<Game>('games', { params: reactive({ page }) });

watch(page, async () => await refresh());

</script>