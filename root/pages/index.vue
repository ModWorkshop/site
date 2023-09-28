<template>
    <page-block :gap="6">
        <flex v-if="!user" class="items-center" column>
            <h1>{{$t('welcome_to_mws')}}</h1>
            <h2>{{$t('mws_short_about')}}</h2>
        </flex>

        <flex v-if="user?.extra?.home_show_last_games ?? true" column :gap="2">
            <flex>
                <span class="h2">{{$t('games')}}</span>
                <a-button class="ml-auto" to="/games">{{$t('view_all_games')}}</a-button>
            </flex>
            <flex v-if="games" class="latest-games gap-2">
                <a-game v-for="game of games.data" :key="game.id" :game="game"/>
            </flex>
        </flex>
        
        <a-lite-mod-list
            v-if="!user"
            :link="`/mods`"
        />
        <mod-list 
            v-else-if="user.extra?.home_show_mods ?? true"
            :title="$t('mods')" 
            title-link="/search/mods"
            :limit="20"
            :url="user ? currentFollowUrl : undefined"
            side-filters
        >
            <template #buttons>
                <button-group v-if="user" v-model:selected="selectedView" button-style="nav">
                    <a-group-button name="all"><i-mdi-layers/> {{$t('all')}}</a-group-button>
                    <a-group-button name="games"><i-mdi-gamepad/> {{$t('followed_games')}}</a-group-button>
                    <a-group-button name="mods"><i-mdi-puzzle/> {{$t('followed_mods')}}</a-group-button>
                    <a-group-button name="users"><i-mdi-users/> {{$t('followed_users')}}</a-group-button>
                    <a-group-button name="liked"><i-mdi-heart/> {{$t('liked')}}</a-group-button>
                </button-group>
            </template>
        </mod-list>
        <thread-list 
            v-if="user?.extra?.home_show_threads ?? true"
            :title="$t('threads')"
            title-link="/forum"
            :forum-id="1"
            :limit="10"
            no-pins
            :lazy="user?.extra?.home_show_mods ?? true"
            :query="false"
            :filters="false"
        />
    </page-block>
</template>

<script setup lang="ts">
import { Game } from '~/types/models';
import { useStore } from '~~/store';

const { user } = useStore();

const { data: games } = await useFetchMany<Game>('games', { params: { limit: 8 } });

const selectedView = ref(user?.extra?.default_mods_view ?? 'all');

const links = {
    mods: 'followed-mods',
    games: 'followed-games/mods',
    users: 'followed-users/mods',
    liked: 'mods/liked',
    all: 'mods',
};
const currentFollowUrl = computed(() => links[selectedView.value]);
</script>