<template>
    <upload-page :game="game"/>
</template>

<script setup lang="ts">
import type { Game } from "~/types/models";

const { game } = defineProps<{
    game: Game
}>();

definePageMeta({
    middleware: 'unbanned-users-only'
});
</script>