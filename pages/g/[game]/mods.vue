<template>
    <mod-list 
        :game="game"
        :url="`games/${game.id}/mods`"
        side-filters
        query
        :default-sort-by="user?.extra?.game_default_mods_sort"
    />
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Game } from '~~/types/models';

definePageMeta({ alias: '/game/:game/mods' });

const { user } = useStore();

const { game } = defineProps<{
    game: Game
}>();
</script>
