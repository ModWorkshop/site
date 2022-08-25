<template>
    <NuxtLink
        :class="{'nav-link': true, 'nav-link-side': side, selected: compTo == root ? $route.path == root : $route.path.startsWith(compTo)}"
        :to="compTo">
        <slot>
            {{title}}
        </slot>
    </NuxtLink>
</template>

<script setup>
    const props = defineProps({
        name: String,
        to: String,
        title: String,
    });

    const side = inject('side');
    const root = inject('root');

    const compTo = computed(() => props.to ? `${root}/${props.to}` : root);
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