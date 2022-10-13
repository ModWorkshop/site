<template>
    <NuxtLink v-if="!last && tos" :to="tos">{{item.name}}</NuxtLink>
    <span v-else>{{item.name}}</span>
</template>

<script setup lang="ts">
import { Breadcrumb } from '~~/types/models';

const props = defineProps<{
    item: Breadcrumb,
    items: Breadcrumb[],
    last: boolean
}>();

function getTo(item: Breadcrumb, i: number) {
    if (item.attachToPrev) {
        const prev = props.items[i-1];
        if (prev) {
            return `${getTo(prev, i-1)}/${item.attachToPrev}`;
        } else {
            return `/${item.attachToPrev}`;
        }
    } else if (item.to) {
        return `/${item.to}`;
    } else if (item.type == 'game') {
        return `/g/${item.id}`;
    } else if (item.type == 'category') {
        const first = props.items[0];
        if (first && first.type == 'game') {
            return getTo(first, 0)+`?category=${item.id}`;
        } else {
            return `/category/${item.id}`;
        }
    } else if (item.type == 'forum_category') {
        const first = props.items[0];
        if (first && first.type == 'game') {
            return getTo(first, 0)+`/forum?category=${item.id}`;
        } else {
            return `/forum?category=${item.id}`;
        }
    } else if (item.type == 'mod') {
        return `/mod/${item.id}`;
    }  else if (item.type == 'thread') {
        return `/thread/${item.id}`;
    }
}

const tos = computed(() => getTo(props.item, props.items.findIndex(i => i == props.item)));
</script>