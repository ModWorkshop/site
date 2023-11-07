<template>
    <admin-edit-game :game="game"/>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-game', props.game);
</script>