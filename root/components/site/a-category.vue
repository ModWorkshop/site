<template>
    <NuxtLink class="category content-block" :to="`/g/${game.short_name}?category=${category.id}`">
        <a-thumbnail v-if="category.thumbnail" url-prefix="games/images" :src="category.thumbnail" :title="category.name"/>
        <flex wrap column class="p-2 items-center">
            {{category.name}}
            <slot/>
        </flex>
    </NuxtLink>
</template>

<script setup lang="ts">
import type { Category, Game } from '~~/types/models';

defineProps<{
    game: Game;
    category: Category;
}>();
</script>