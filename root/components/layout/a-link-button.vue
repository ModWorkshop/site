<template>
    <component :is="link ? NuxtLink : 'span'" :to="link" class="link-button">
        <a-icon v-if="icon" :icon="icon" :size="iconSize" :style="iconStyle"/> <slot/>
    </component>
</template>

<script setup lang="ts">
const props = defineProps<{
    href?: string,
    to?: string,
    iconSize?: string,
    icon?: Component,
    iconRotation?: number,
}>();

const link = computed(() => props.to ?? props.href);
const NuxtLink = resolveComponent('NuxtLink');

const iconStyle = computed(() => ({
    transform: props.iconRotation ? `Rotate(${props.iconRotation}deg)` : null
}));
</script>

<style scoped>
.link-button {
    transition: color 0.15s ease-in-out;
    color: var(--text-color);
}

.link-button:hover {
    cursor: pointer;
    color: var(--primary-hover-color);
}
</style>