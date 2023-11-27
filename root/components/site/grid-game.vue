<template>
    <NuxtLink class="game content-block" :to="`/g/${game.short_name}`">
        <a-thumbnail url-prefix="games/images" :src="game.thumbnail" :title="game.name"/>
        <m-flex wrap column class="p-2 items-center">
            {{game.name}}
            <span v-if="game.mods_count || game.mods_count === 0" class="text-secondary">{{$t('mod_count', { n: game.mods_count })}}</span>
        </m-flex>
    </NuxtLink>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';

defineProps<{
    game: Game
}>();
</script>