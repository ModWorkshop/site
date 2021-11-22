<template>
    <span :class="{tag: true, 'tag-small': small}" :style="{backgroundColor: color, color: textColor}"><slot/></span>
</template>
<script>
import chroma from 'chroma-js';
export default {
    props: {
        color: String,
        small: Boolean
    },
    computed: {
        textColor() {
            if (this.color) {
                try {
                    const contrast = chroma.contrast('#000', this.color);
                    if (contrast > 4.55) {
                        return '#000';
                    } else {
                        return '#fff';
                    }
                } catch (error) {
                    return '#fff';
                }
            }
            return null;
        }
    }
};
</script>
<style scoped>
    .tag {
        color: #000;
        padding: 0.25rem 0.75rem;
        font-size: 70%;
        background: var(--primary-color);
        border-radius: var(--border-radius);
    }

    .tag-small {
        padding: 0.15rem 0.4rem;
    }
</style>