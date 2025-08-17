<template>
    <NuxtLink class="category" :to="`/g/${game.short_name}?category=${category.id}`">
        <m-card center :padding="2">
            <template #thumbnail>
                <a-thumbnail v-if="category.thumbnail" url-prefix="games/images" :src="category.thumbnail" :title="category.name"/>
            </template>
            {{category.name}}
        </m-card>
    </NuxtLink>
</template>

<script setup lang="ts">
import type { Category, Game } from '~/types/models';

defineProps<{
    game: Game;
    category: Category;
}>();
</script>