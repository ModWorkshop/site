<template>
    <flex gap="2" class="breadcrumbs">
        <template v-for="(item, i) in crumbs">
            <nuxt-link v-if="item.href && i != items.length - 1" :key="item.name" :to="item.href">{{item.name}}</nuxt-link>
            <span v-else :key="item.name + '_no_link'">{{item.name}}</span>
            <span v-if="i != items.length - 1" :key="`split_${item.name}`"> / </span>
        </template>
    </flex>
</template>

<script setup>
import { computed } from '@nuxtjs/composition-api';

const props = defineProps({
    items: Array
});
const crumbs = computed(() => props.items.slice().reverse());
</script>

<style scoped>
    .breadcrumbs {
        color: var(--text-inactive);
    }
</style>