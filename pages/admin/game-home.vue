<template>
    <h3>This is the game settings page. The page contains a bunch of settings regarding the game.</h3>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';
const props = defineProps<{
    game: Game
}>();
</script>