<template>
    <img :src="compSrc" :width="width" :height="height" :alt="alt">
</template>

<script setup>
    import { computed } from '@nuxtjs/composition-api';

    const props = defineProps({
        src: {
            default: '',
            type: String,
        },
        width: [Number, String],
        height: [Number, String],
        alt: String
    });

    const compSrc = computed(function() {
        const src = props.src;
        //This sucks lol
        if (src.startsWith("http://") || src.startsWith("data:")) {
            return src;
        } else {
            return `http://127.0.0.1:8000/storage/${src}`;
        }
    });
</script>