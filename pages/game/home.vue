<template>
    <flex v-if="game" column gap="4">
        <mod-list 
            v-if="user?.extra.game_show_mods ?? true"
            :title="$t('mods')"
            :limit="10"
            :title-link="`/g/${game.short_name}/mods`"
            :game="game"
            :url="`games/${game.id}/mods`"
        />
        <mini-thread-list 
            v-if="user?.extra.game_show_threads ?? true"
            :title="$t('threads')"
            :title-link="`/g/${game.short_name}/forum`"
            :query="false"
            :game-id="game?.id"
            :forum-id="game?.forum_id"
        />
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game } from '~~/types/models';

const { user } = useStore();

defineProps<{
    game: Game
}>();
</script>