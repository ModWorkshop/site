<template>
    <flex column gap="3">
        <mod-list 
            v-if="user?.extra?.game_show_mods ?? true"
            :title="$t('search_mods_game', [,game.name])"
            :title-link="`/g/${game.short_name}/mods`"
            side-filters
            query
            :game="game"
        />
        <thread-list 
            v-if="user?.extra?.game_show_threads ?? true"
            :title="$t('search_threads_game', [,game.name])"
            :title-link="`/g/${game.short_name}/forum`"
            :game-id="game?.id"
            :forum-id="game?.forum_id"
            :limit="10"
            no-pins
            :lazy="user?.extra?.game_show_mods ?? true"
            :query="false"
            :filters="false"
        />
    </flex>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { Game } from '~~/types/models';
const store = useStore();
const { user } = store;

definePageMeta({ alias: '/game/:game' });

const { game } = defineProps<{
    game: Game
}>();

watch(() => game, () =>  store.currentGame = game, { immediate: true });
</script>