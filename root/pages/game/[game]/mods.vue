<template>
    <mod-list :game="game" :url="`games/${game.id}/mods`" side-filters query/>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

definePageMeta({ alias: '/g/:game/mods' });

const { game } = defineProps<{
    game: Game
}>();
</script>
