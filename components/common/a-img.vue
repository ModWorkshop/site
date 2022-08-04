<template>
    <img :src="compSrc" :width="width" :height="height" :alt="alt">
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const props = defineProps({
    src: {
        default: '',
        type: String,
    },
    urlPrefix: {
        type: String,
        default: ''
    },
    width: [Number, String],
    height: [Number, String],
    alt: String
});

const compSrc = computed(function() {
    const src = props.src;
    //This sucks lol
    if (src && (src.startsWith("http://") || src.startsWith("https://") || src.startsWith("data:"))) {
        return src;
    } else {
        return `${config.apiUrl}/storage/${props.urlPrefix || ''}${src}`;
    }
});
</script>