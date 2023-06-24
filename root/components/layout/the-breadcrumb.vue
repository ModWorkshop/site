<template>
    <flex gap="2" class="breadcrumb p-3 md:p-0">
        <template v-for="(item, i) in items" :key="`${item.name}-${i}`">
            <breadcrumb-link :item="item" :items="items" :last="i == (items.length - 1)"/>
            <span v-if="i != items.length - 1" :key="`split_${item.name}`"> / </span>
        </template>
    </flex>
</template>

<script setup lang="ts">
import { Breadcrumb } from '../../types/models';

defineProps<{
    items: Breadcrumb[]
}>();
</script>

<style scoped>
    .breadcrumb {
        color: var(--text-inactive);
        width: 100%;
        overflow-x: auto;
    }

    .breadcrumb > * {
        white-space: nowrap;
    }
</style>