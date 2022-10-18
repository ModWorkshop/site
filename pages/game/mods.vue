<template>
    <mod-list :game="game" :url="`games/${game.id}/mods`" side-filters/>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

defineProps<{
    game: Game
}>();
</script>