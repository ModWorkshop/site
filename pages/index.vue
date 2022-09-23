<template>
    <page-block>
        <template v-if="store.user">
            <button-group v-model:selected="selectedFollow" button-style="nav">
                <a-group-button name="games" icon="chess-board">Followed Games</a-group-button>
                <a-group-button name="mods" icon="tools">Followed Mods</a-group-button>
                <a-group-button name="users" icon="users">Followed Users</a-group-button>
                <a-group-button name="liked" icon="heart">Liked Mods</a-group-button>
            </button-group>
            <mod-list :url="currentFollowUrl"/>
        </template>
        <template v-else>
            <flex column gap="3">
                <flex class="items-center" column>
                <h1>Welcome to ModWorkshop!</h1>
                <h2>We are a modding site that that embraces open source for modding!</h2>
            </flex>
            <h2>Last Updated Games</h2>
                <content-block class="games-grid gap-2">
                    <NuxtLink v-for="game of lastGames" :key="game.id" :to="`/g/${game.short_name}`">
                        <a-img class="ratio-image round" :src="game.thumbnail ? `games/thumbnails/${game.thumbnail}` : 'assets/nopreview.webp'"/>
                        {{game.name}}
                    </NuxtLink>
                </content-block>
                <a-button class="mx-auto" to="/games">View all games</a-button>
            </flex>
            <h2>{{$t('latest_mods')}}</h2>
            <content-block padding="4">
                <static-mod-list name="latest" :params="{ sort_by: 'score', limit: 10 }"/>
            </content-block>
            <h2>{{$t('popular_mods')}}</h2>
            <content-block padding="4">
                <static-mod-list name="popular" :params="{ sort_by: 'score', limit: 10 }"/>
            </content-block>
        </template>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';

const store = useStore();

await store.fetchGames();

const allGames = ref(false);

const selectedFollow = ref('games');

const currentFollowUrl = computed(() => {
    if (selectedFollow.value === 'games') {
        return 'followed-games/mods';
    } else if (selectedFollow.value === 'users') {
        return 'followed-users/mods';
    } else if (selectedFollow.value === 'mods') {
        return 'followed-mods';
    } else if (selectedFollow.value === 'liked') {
        return 'mods/liked';
    }
});

const lastGames = computed(() => {
    if (allGames.value) {
        return store.games.data;
    } else {
        return store.games.data.slice(0, 10);
    }
});
</script>