<template>
    <NuxtLink class="category content-block" :to="`/g/${game.short_name}?category=${category.id}`">
        <a-thumbnail v-if="category.thumbnail" url-prefix="games/images" :src="category.thumbnail" :title="category.name"/>
        <m-flex wrap column class="p-2 items-center">
            {{category.name}}
            <slot/>
        </m-flex>
    </NuxtLink>
</template>

<script setup lang="ts">
import type { Category, Game } from '~~/types/models';

defineProps<{
    game: Game;
    category: Category;
}>();
</script>