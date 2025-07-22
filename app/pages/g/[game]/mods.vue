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
import type { Game } from '~/types/models';
import { useStore } from '~/store';

definePageMeta({ alias: '/game/:game/mods' });

const { user } = useStore();

const { game } = defineProps<{
    game: Game
}>();
</script>
