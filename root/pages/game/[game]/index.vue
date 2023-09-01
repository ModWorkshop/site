<template>
    <div>
        <a-lite-mod-list
            v-if="user?.extra?.game_show_mods ?? true"
            :link="`/g/${game.short_name}/mods`"
            :game="game"
        />
        <thread-list 
            v-if="user?.extra?.game_show_threads ?? true"
            :title="$t('threads')"
            :title-link="`/g/${game.short_name}/forum`"
            :game-id="game?.id"
            :forum-id="game?.forum_id"
            :limit="10"
            no-pins
            :lazy="user?.extra?.game_show_mods ?? true"
            :query="false"
            :filters="false"
        />
    </div>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { Game } from '~~/types/models';
const store = useStore();
const { user } = store;

definePageMeta({ alias: '/g/:game' });

const { game } = defineProps<{
    game: Game
}>();

watch(() => game, () =>  store.currentGame = game, { immediate: true });
</script>