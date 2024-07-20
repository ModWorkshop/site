<template>
    <admin-edit-game v-model="game"/>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';

const game = defineModel<Game>('game', { required: true });
useNeedsPermission('manage-game', game.value);
</script>