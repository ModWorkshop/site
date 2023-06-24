<template>
    <NuxtLink class="content-block" :to="`/g/${game.short_name}`">
        <a-thumbnail url-prefix="games/images" :src="game.thumbnail" :title="game.name"/>
        <flex wrap class="p-2">
            {{game.name}}
            <span class="text-secondary ml-auto">{{$t('mod_count', { n: game.mod_count })}}</span>
        </flex>
    </NuxtLink>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

defineProps<{
    game: Game
}>();
</script>