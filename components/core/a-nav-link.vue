<template>
    <nuxt-link
        :class="{'nav-link': true, 'nav-link-side': side, selected: to == root ? $route.path == root : $route.path.startsWith(to)}"
        :to="to">
        {{title}}
    </nuxt-link>
</template>

<script setup>
    defineProps({
        name: String,
        to: String,
        title: String,
    });

    const side = inject('side');
    const root = inject('root');
</script>

<style>
    .nav-link {
        font-size: 1.125rem;
        border-radius: 4px;
        padding: 0.75rem 2rem;
        color: var(--secondary-text-color);
    }

    .nav-link:hover {
        cursor: pointer;
    }

    .nav-link.selected {
        background-color: var(--tab-selected-color);
        color: var(--primary-color);
    }

    .nav-link-side {
        padding: 0.75rem 1.5rem;
        min-width: 150px;
    }
</style>