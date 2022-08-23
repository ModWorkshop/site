<template>
    <page-block>
        <mod-list v-if="store.user"/>
        <template v-else-if="!store.user">
            <flex column gap="3">
                <flex class="items-center" column>
                    <div class="text-3xl">Welcome to ModWorkshop!</div>
                    <h3>To begin, pick a game you wish to mod</h3>
                </flex>
                <content-block class="games-grid gap-2">
                    <NuxtLink v-for="game of lastGames" :key="game.id" :to="`/g/${game.short_name}`">
                        <a-img class="ratio-image round" :src="game.thumbnail ? `games/thumbnails/${game.thumbnail}` : 'assets/nopreview.webp'"/>
                        {{game.name}}
                    </NuxtLink>
                </content-block>
                <a-button class="mx-auto" @click="allGames = true">View all games</a-button>
                <flex class="items-center" column>
                    <h3>
                        Don't see your game? Submit game request <a>here</a>
                    </h3>
                </flex>
            </flex>
            <content-block padding="4">
                <NuxtLink class="text-3xl">{{$t('latest_mods')}}</NuxtLink>
                <static-mod-list name="latest" :params="{ sort_by: 'score', limit: 5 }"/>
            </content-block>
            <content-block padding="4">
                <NuxtLink class="text-3xl">{{$t('popular_mods')}}</NuxtLink>
                <static-mod-list name="popular" :params="{ sort_by: 'score', limit: 5 }"/>
            </content-block>
        </template>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';

const store = useStore();

await store.fetchGames();

const allGames = ref(false);

const lastGames = computed(() => {
    if (allGames.value) {
        return store.games.data;
    } else {
        return store.games.data.slice(0, 10);
    }
});
</script>