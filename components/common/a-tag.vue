<template>
    <span :class="{tag: true, 'tag-small': small, round: !capsule, capsule}" :style="{backgroundColor: color, color: textColor}"><slot/></span>
</template>
<script setup>
import { getContrast } from 'polished';

const props = defineProps({
    small: Boolean,
    color: String,
    capsule: Boolean
});

const textColor = computed(() => {
    const color = props.color;
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
    display: inline-block;
    color: #000;
    padding: 0.5rem 0.75rem;
    font-size: 70%;
    font-weight: 500;
    background: var(--primary-color);
    line-height: 1.25;
}

.tag-small {
    padding: 0.25rem 0.4rem;
}

.capsule {
    border-radius: 1rem;
}
</style>