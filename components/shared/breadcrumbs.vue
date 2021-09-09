<template>
    <flex gap="2" class="breadcrumbs">
        <template v-for="(item, i) in crumbs">
            <nuxt-link v-if="item.href && i != items.length - 1" :key="item.name" :to="item.href">{{item.name}}</nuxt-link>
            <span v-else :key="item.name">{{item.name}}</span>
            <span v-if="i != items.length - 1" :key="`split_${item.name}`"> / </span>
        </template>
    </flex>
</template>

<script>
    export default {
        props: {
            items: Array
        },
        computed: {
            crumbs() {
                return this.items.slice().reverse();
            }
        }
    }
</script>

<style scoped>
    .breadcrumbs {
        color: var(--text-inactive);
    }
</style>