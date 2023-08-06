<template>
    <upload-page :game="game"/>
</template>

<script setup lang="ts">
import { Game } from "types/models";

const { game } = defineProps<{
    game: Game
}>();

</script>