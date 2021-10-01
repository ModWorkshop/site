<template>
    <nuxt-link
        :class="{'tab-link': true, 'tab-link-side': side, selected: $route.path.startsWith(to)}"
        :to="to">
        {{title}}
    </nuxt-link>
</template>

<script setup>
    import { inject } from '@nuxtjs/composition-api';

    defineProps({
        name: String,
        to: String,
        title: String,
    });

    const side = inject('side');
</script>

<style>
    .tab-link {
        border-radius: 4px;
        padding: 0.5rem 2rem;
        color: var(--secondary-text-color);
    }

    .tab-link:hover {
        cursor: pointer;
    }

    .tab-link.selected {
        background-color: var(--tab-selected-color);
        color: var(--primary-color);
    }

    .tab-link-side {
        padding: 0.5rem 4rem 0.5rem 0.75rem;
        min-width: 200px;
    }
</style>