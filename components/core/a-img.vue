<template>
    <img :src="compSrc" :width="width" :height="height" :alt="alt">
</template>

<script setup>
const { src } = defineProps({
    src: {
        default: '',
        type: String,
    },
    width: [Number, String],
    height: [Number, String],
    alt: String
});

const compSrc = computed(function() {
    //This sucks lol
    if (src && (src.startsWith("http://") || src.startsWith("https://") || src.startsWith("data:"))) {
        return src;
    } else {
        return `http://localhost:8000/storage/${src}`;
    }
});
</script>