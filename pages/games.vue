<template>
    <page-block>
        <flex column class="items-center">
            <h2>We host {{games.meta.total}} games as of now.</h2>
            <h3>Want your favorite game to be added? Want to moderate it? Submit a request <a>here</a></h3>
        </flex>
        <a-pagination v-model="page" per-page="50" set-query :total="games.meta.total"/>
        <content-block class="games-grid gap-2">
            <NuxtLink v-for="game of games.data" :key="game.id" :to="`/g/${game.short_name}`">
                <a-img class="ratio-image round" :src="game.thumbnail ? `games/thumbnails/${game.thumbnail}` : 'assets/nopreview.webp'"/>
                {{game.name}}
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

<style scoped>

</style>