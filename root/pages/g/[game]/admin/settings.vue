<template>
    <admin-edit-game v-model="gameCopy" @update:model-value="updatePage"/>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';
import clone from 'rfdc/default';

const { game } = defineProps<{
    game: Game;
}>();

const gameCopy = ref<Game>(clone(game));
useNeedsPermission('manage-game', game);

// Unfortunately I don't think you can do v-model with NuxtPage, 
// It causes some weird breakage of reactivity After saving once.
function updatePage() {
    Object.assign(game, gameCopy.value);
}
</script>