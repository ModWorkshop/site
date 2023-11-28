<template>
    <div class="nav">
        <m-flex v-if="side" class="items-center hidden max-lg:block" @click="menuOpen = !menuOpen">
            <m-link class="collapse-button">
                <i-mdi-menu/>
            </m-link>
        </m-flex>
        <m-flex :class="{'menu-open': menuOpen}" :column="!side" :gap="2">
            <div v-if="menuOpen" class="menu-closer" @click.prevent="menuOpen = false"/>
            <Transition name="left-slide">
                <m-flex v-show="!side || menuOpen" class="nav-menu p-6" :column="side">
                    <slot/>
                </m-flex>
            </Transition>
            <m-content-block class="nav-menu-content" column grow gap="3" padding="6">
                <slot name="content"/>
            </m-content-block>
        </m-flex>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    side: Boolean,
    root: String,
    padding: {
        default: 2,
        type: [String, Number]
    }
});

const menuOpen = ref(false);

provide('root', props.root);
provide('side', props.side);
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