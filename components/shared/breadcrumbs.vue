<template>
    <flex gap="2" class="breadcrumbs">
        <template v-for="(item, i) in items">
            <nuxt-link v-if="item.to" :key="item.name" :to="item.to">{{item.name}}</nuxt-link>
            <span v-else :key="item.name">{{item.name}}</span>
            <span v-if="i != items.length - 1" :key="`split_${item.name}`"> / </span>
        </template>
    </flex>
</template>

<script>
    export default {
        props: {
            items: Array
        }
    }
</script>

<style scoped>
    .breadcrumbs {
        color: var(--text-inactive);
    }
</style>