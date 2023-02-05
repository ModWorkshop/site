<template>
    <h3>This is the game settings page. The page contains a bunch of settings regarding the game.</h3>
    <a-alert color="warning" :to="`/admin/${getGameResourceUrl('reports', props.game)}`">
        There are 9999 reports!
    </a-alert>
    <a-alert :to="`/admin/${getGameResourceUrl('approvals', props.game)}`">
        There are 9999 mods waiting for approval
    </a-alert>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';
const props = defineProps<{
    game: Game
}>();
</script>