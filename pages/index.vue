<template>
    <page-block :gap="6">
        <flex v-if="!user" class="items-center" column>
            <h1>{{$t('welcome_to_mws')}}</h1>
            <h2>{{$t('mws_short_about')}}</h2>
        </flex>

        <flex v-if="user?.extra.home_show_last_games ?? true" column :gap="2">
            <flex>
                <h2>{{$t('last_updated_games')}}</h2>
                <a-button class="ml-auto" to="/games">{{$t('view_all_games')}}</a-button>
            </flex>
            <flex class="games-grid gap-2">
                <a-game v-for="game of lastGames" :key="game.id" :game="game"/>
            </flex>
        </flex>
        
        <mod-list v-if="user?.extra.home_show_mods ?? true" :title="$t('mods')" title-link="/search/mods" :limit="20" :url="user ? currentFollowUrl : undefined">
            <template #buttons>
                <button-group v-if="user" v-model:selected="selectedView" button-style="nav">
                    <a-group-button name="games" icon="mdi:gamepad">{{$t('followed_games')}}</a-group-button>
                    <a-group-button name="mods" icon="mdi:puzzle">{{$t('followed_mods')}}</a-group-button>
                    <a-group-button name="users" icon="mdi:users">{{$t('followed_users')}}</a-group-button>
                    <a-group-button name="liked" icon="mdi:heart">{{$t('liked')}}</a-group-button>
                    <a-group-button name="all" icon="mdi:layers">{{$t('all')}}</a-group-button>
                </button-group>
            </template>
        </mod-list>
        <thread-list 
            v-if="user?.extra.home_show_threads ?? true"
            :title="$t('threads')"
            title-link="/forum"
            :forum-id="1"
            :limit="10"
            :query="false"
            :filters="false"
        />
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game } from '~~/types/models';

const { user } = useStore();

const { data: games } = await useFetchMany<Game>('games', { initialCache: true });

const allGames = ref(false);

const selectedView = ref(user?.extra.default_mods_view ?? 'all');

const links = {
    mods: 'followed-mods',
    games: 'followed-games/mods',
    users: 'followed-users/mods',
    liked: 'mods/liked',
    all: 'mods',
};
const currentFollowUrl = computed(() => links[selectedView.value]);

const lastGames = computed(() => {
    if (allGames.value) {
        return games.value.data;
    } else {
        return games.value.data.slice(0, 6);
    }
});
</script>