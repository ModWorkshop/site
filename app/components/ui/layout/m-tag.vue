<template>
    <span :class="{tag: true, 'tag-small': small, capsule}" 
        :style="{
            backgroundColor: bgColor,
            color: textColor,
        }"
    >
        <slot/>
    </span>
</template>
<script setup lang="ts">
import Color from 'colorjs.io';
import { useStore } from '~/store';

const { small, color, capsule = false } = defineProps<{
    small?: boolean,
    color?: string,
    capsule?: boolean
}>();

const store = useStore();

const bgColor = computed(() => {
    if (color) {
        const col = new Color(color);
        col.alpha = 0.25;

        return col
    } else {
        return '#fff';
    }
});

const textColor = computed(() => {
    if (color) {
        try {
            const contrast = getContrast('#000', color.replaceAll(' ', ''));
            const col = new Color(color);

            if (store.theme == 'dark') {
                col.hsl.l = 75;
            } else {
                col.hsl.l = 40;
            }

            if (contrast < 5.5) {
                return col;
            } else {
                return col;
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
    font-weight: bold;
    background: var(--primary-color);
    border-radius: 16px;
}

.tag-small {
    padding: 0.45rem;
    line-height: 1;
}

.capsule {
    border-radius: 1rem;
}
</style>