<template>
    <flex class="nav" column gap="3">
        <flex v-if="side" class="items-center hidden max-lg:block" @click="menuOpen = !menuOpen">
            <a-link-button class="collapse-button" icon="bars"/>
        </flex>
        <flex :class="{'menu-open': menuOpen}" :column="!side" :gap="side ? 8 : 2">
            <div v-if="menuOpen" class="menu-closer" @click.prevent="menuOpen = false"/>
            <Transition name="left-slide">
                <flex v-show="!side || menuOpen" class="nav-menu" :column="side">
                    <slot/>
                </flex>
            </Transition>
            <flex class="nav-menu-content" column grow gap="3">
                <slot name="content"/>
            </flex>
        </flex>
    </flex>
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

<style>
.nav-menu-content {
    flex-wrap: wrap;
    overflow-x: auto;
}
</style>