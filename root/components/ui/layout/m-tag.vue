<template>
    <span :class="{tag: true, 'tag-small': small, capsule}" 
        :style="{
            backgroundColor: color,
            color: textColor
        }"
    >
        <slot/>
    </span>
</template>
<script setup lang="ts">
const { small, color, capsule = false } = defineProps<{
    small?: boolean,
    color?: string,
    capsule?: boolean
}>();

const textColor = computed(() => {
    if (color) {
        try {
            const contrast = getContrast('#000', color.replaceAll(' ', ''));
            if (contrast < 4.5) {
                return '#fff';
            } else {
                return '#000';
            }
        } catch (error) {
            return 'var(--primary-color-text)';
        }
    }
    return 'var(--primary-color-text)';
});

</script>
<style scoped>
.tag {
    display: inline-flex;
    color: #000;
    text-shadow: none;
    padding: 0.5rem;
    gap: 4px;
    align-items: center;
    justify-content: center;
    font-size: 75%;
    font-weight: 400;
    background: var(--primary-color);
    border-radius: var(--tag-border-radius);
}

.tag-small {
    padding: 0.4rem;
    line-height: 1;
}

.capsule {
    border-radius: 1rem;
}
</style>