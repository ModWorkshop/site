<template>
    <NuxtLink :class="classes" :to="compTo" @click="clickLink">
        <a-icon v-if="icon" :icon="icon"/> <slot>{{title}}</slot>
    </NuxtLink>
</template>

<script setup lang="ts">
const props = defineProps<{
    name: string,
    to: string,
    icon: Component,
    title: string,
    selected: boolean,
}>();

const route = useRoute();

const side = inject<boolean>('side', false);
const menuOpen = inject<Ref<boolean>>('menuOpen', null);
const root = inject<string>('root', '');

const compTo = computed(() => props.to ? `${root}/${props.to}` : root);

const classes = computed(() => ({
    'nav-link': true,
    'nav-link-side': side,
    selected: props.selected || (compTo.value == root ? route.path == root : route.path.startsWith(compTo.value))
}));

function clickLink() {
    if (menuOpen) {
        menuOpen.value = false;
    }
}
</script>

<style>
.nav-link {
    font-size: 1.125rem;
    border-radius: 4px;
    padding: 0.75rem 2rem;
    color: var(--secondary-text-color);
    transition: ease-in-out 0.15s;
    transition-property: color, background-color;
}

.nav-link:hover {
    cursor: pointer;
    color: var(--primary-color);
}

.nav-link.selected {
    background-color: var(--tab-selected-color);
    color: var(--primary-color);
}

.nav-link-side {
    padding: 0.75rem 1.25rem;
    min-width: 200px;
}
</style>