<template>
    <div>
        <h3>This is the game settings page. The page contains a bunch of settings regarding the game.</h3>
    </div>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';
defineProps<{
    game: Game
}>();
</script>