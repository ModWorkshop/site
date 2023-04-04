<template>
    <span :class="{tag: true, 'tag-small': small, round: !capsule, capsule}" 
        :style="{
            backgroundColor: color,
            color: textColor
        }"
    >
        <slot/>
    </span>
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
    padding: 0.5rem;
    gap: 4px;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 500;
    background: var(--primary-color);
}

.tag-small {
    padding: 0.15rem 0.4rem;
    line-height: 1.25;
}

.capsule {
    border-radius: 1rem;
}
</style>