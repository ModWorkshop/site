<template>
    <strong :class="{tag: true, 'tag-small': small}" :style="{backgroundColor: color, color: textColor}"><slot/></strong>
</template>
<script setup>
import { getContrast } from 'polished';

const { color } = defineProps({
    small: Boolean,
    color: String
});

const textColor = computed(() => {
    if (color) {
        try {
            const contrast = getContrast('#000', color.replaceAll(' ', ''));
            if (contrast < 4.5) {
                return '#fff';
            }
        } catch (error) {
            return '#fff';
        }
    }
    return '#000';
});

</script>
<style scoped>
    .tag {
        color: #000;
        padding: 0.5rem 0.75rem;
        font-size: 70%;
        background: var(--primary-color);
        border-radius: var(--border-radius);
    }

    .tag-small {
        padding: 0.15rem 0.4rem;
    }
</style>