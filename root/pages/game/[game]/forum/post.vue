<template>
    <edit-thread-page :game="game"/>
</template>

<script lang="ts" setup>
import { Game } from '../../../../types/models';

definePageMeta({ alias: '/g/:game/forum/post' });

definePageMeta({
    middleware: 'unbanned-users-only'
});

const { game } = defineProps<{
    game: Game
}>();
</script>