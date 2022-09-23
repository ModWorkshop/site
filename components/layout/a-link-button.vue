<template>
    <NuxtLink class="link-button">
        <font-awesome-icon v-if="icon" :icon="icon" :size="iconSize" :style="iconStyle"/> <slot/>
    </NuxtLink>
</template>

<script setup lang="ts">
const props = defineProps<{
    iconSize?: string,
    icon?: string | Array<any>,
    iconRotation?: number,
}>();

const iconStyle = computed(() => ({
    transform: props.iconRotation ? `Rotate(${props.iconRotation}deg)` : null
}));
</script>

<style scoped>
.link-button {
    color: var(--text-color);
}

.link-button:hover {
    cursor: pointer;
    color: var(--primary-hover-color);
}
</style>