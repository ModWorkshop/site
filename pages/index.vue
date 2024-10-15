<template>
    <page-block :gap="6">
        <m-flex v-if="!user" class="items-center" column>
            <h1>{{$t('welcome_to_mws')}}</h1>
            <h2>{{$t('mws_short_about')}}</h2>
        </m-flex>

        <m-flex v-if="user?.extra?.home_show_last_games ?? true" column :gap="2">
            <m-flex>
                <span class="h2">{{$t('games')}}</span>
                <m-button class="ml-auto" to="/games">{{$t('view_all_games')}}</m-button>
            </m-flex>
            <m-flex v-if="games" class="latest-games gap-2">
                <grid-game v-for="game of games.data" :key="game.id" :game="game" :lazy-thumbnails="false"/>
            </m-flex>
        </m-flex>

        <lite-mod-list
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
                <m-toggle-group v-if="user" v-model:selected="selectedView" button-style="nav" :wrap="false" class="overflow-auto">
                    <m-flex class="flex-shrink-0">
                        <m-toggle-group-item value="all"><i-mdi-layers/> {{$t('all')}}</m-toggle-group-item>
                        <m-toggle-group-item value="games"><i-mdi-gamepad/> {{$t('followed_games')}}</m-toggle-group-item>
                        <m-toggle-group-item value="mods"><i-mdi-puzzle/> {{$t('followed_mods')}}</m-toggle-group-item>
                        <m-toggle-group-item value="users"><i-mdi-users/> {{$t('followed_users')}}</m-toggle-group-item>
                        <m-toggle-group-item value="liked"><i-mdi-heart/> {{$t('liked')}}</m-toggle-group-item>
                    </m-flex>
                </m-toggle-group>
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
import type { Game } from '~/types/models';
import { useStore } from '~~/store';

const { user } = useStore();

const { data: games } = await useFetchMany<Game>('games', { params: { limit: 7 } });

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

<style>
.latest-games .game {
    max-width: 230px;
    flex: 1;
}
</style>