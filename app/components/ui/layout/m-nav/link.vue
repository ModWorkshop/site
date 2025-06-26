<template>
    <NuxtLink :class="classes" :to="compTo" @click="clickLink">
        <m-icon v-if="icon" :icon="icon"/> <slot>{{title}}</slot>
    </NuxtLink>
</template>

<script setup lang="ts">
const props = defineProps<{
    name?: string,
    to?: string,
    alias?: string,
    icon?: Component,
    title?: string,
    selected?: boolean,
}>();

const route = useRoute();

const side = inject<boolean>('side', false);
const menuOpen = inject<Ref<boolean>>('menuOpen', ref(false));
const root = inject<string>('root', '');

const compAlias = computed(() => {
    if (typeof(props.alias) == 'string') {
        return props.alias ? `${root}/${props.alias}` : root;
    }
});

const normalizedRoot = computed(() => root + '/');
const normalizedPath = computed(() => route.path + '/');

const compTo = computed(() => props.to ? `${root}/${props.to}` : root);

const classes = computed(() => ({
    'nav-link': true,
    'nav-link-side': side,
    selected: 
        props.selected 
        || (compTo.value == root ? normalizedPath.value == normalizedRoot.value : normalizedPath.value.startsWith(compTo.value + '/'))
        || (compAlias.value == root ? route.path == root : (compAlias.value && route.path.startsWith(compAlias.value)))
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
    color: var(--text-color);
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