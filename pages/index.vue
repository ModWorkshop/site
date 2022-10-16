<template>
    <page-block>
        <template v-if="store.user">
            <button-group v-model:selected="selectedFollow" button-style="nav">
                <a-group-button name="games" icon="chess-board">{{$t('games')}}</a-group-button>
                <a-group-button name="mods" icon="tools">{{$t('following')}}</a-group-button>
                <a-group-button name="users" icon="users">{{$t('followed_users')}}</a-group-button>
                <a-group-button name="liked" icon="heart">{{$t('liked')}}</a-group-button>
                <a-group-button name="all" icon="question">{{$t('all')}}</a-group-button>
            </button-group>
            <mod-list :url="currentFollowUrl"/>
        </template>
        <template v-else>
            <flex column gap="3">
                <flex class="items-center" column>
                <h1>Welcome to ModWorkshop!</h1>
                <h2>We are a modding site that that embraces open source for modding!</h2>
            </flex>
            <h2>{{$t('last_updated_games')}}</h2>
                <content-block class="games-grid gap-2">
                    <NuxtLink v-for="game of lastGames" :key="game.id" :to="`/g/${game.short_name}`">
                        <a-img class="ratio-image round" :src="game.thumbnail ? `games/thumbnails/${game.thumbnail}` : 'assets/nopreview.webp'"/>
                        {{game.name}}
                    </NuxtLink>
                </content-block>
                <a-button class="mx-auto" to="/games">{{$t('view_all_games')}}</a-button>
            </flex>
            <h2>{{$t('latest_mods')}}</h2>
            <content-block padding="4">
                <static-mod-list name="latest" :params="{ sort: 'score', limit: 10 }"/>
            </content-block>
            <h2>{{$t('popular_mods')}}</h2>
            <content-block padding="4">
                <static-mod-list name="popular" :params="{ sort: 'score', limit: 10 }"/>
            </content-block>
        </template>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game } from '~~/types/models';


const store = useStore();

const { data: games } = await useFetchMany<Game>('games', { initialCache: true });

const allGames = ref(false);

const selectedFollow = ref('games');

const links = {
    mods: 'followed-mods',
    games: 'followed-games/mods',
    users: 'followed-users/mods',
    liked: 'mods/liked',
    all: 'mods',
};
const currentFollowUrl = computed(() => links[selectedFollow.value]);

const lastGames = computed(() => {
    if (allGames.value) {
        return games.value.data;
    } else {
        return games.value.data.slice(0, 10);
    }
});
</script>