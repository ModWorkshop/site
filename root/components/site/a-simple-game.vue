<template>
    <flex class="items-center" gap="2">
        <a-thumbnail class="ratio-image round" style="width: 100px;" url-prefix="games/images" :src="game.thumbnail"/>
        <span>{{game.name}}</span>
    </flex>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

defineProps<{
    game: Game
}>();
</script>