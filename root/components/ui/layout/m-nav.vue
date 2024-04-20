<template>
    <div class="nav">
        <m-flex v-if="side" class="items-center hidden max-lg:block" @click="menuOpen = !menuOpen">
            <m-link class="collapse-button">
                <i-mdi-menu/>
            </m-link>
        </m-flex>
        <m-flex :class="{'menu-open': menuOpen}" :column="!side" :gap="2" :padding="padding">
            <div v-if="menuOpen" class="menu-closer" @click.prevent="menuOpen = false"/>
            <Transition name="left-slide">
                <m-flex v-show="!side || menuOpen" :class="{'nav-menu': true, 'p-6': side}" :column="side">
                    <slot/>
                </m-flex>
            </Transition>
            <m-flex column grow gap="3" :padding="6" :class="{'content-block': background, 'overflow-x-auto': true}">
                <slot name="content"/>
            </m-flex>
        </m-flex>
    </div>
</template>

<script setup lang="ts">
const { padding = 2, side = false, background = true, root } = defineProps<{
    side?: boolean;
    root?: string;
    padding?: string|number,
    background?: boolean;
}>();

const menuOpen = ref(false);

provide('root', root);
provide('side', side);
provide('menuOpen', menuOpen.value);
</script>

<style scoped>
.nav {
    display: flex;
    grid-gap: 12px;
    gap: 12px;
    overflow-x: hidden;
    flex-grow: 1;
    flex-direction: column;
}
</style>