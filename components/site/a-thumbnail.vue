<template>
    <picture>
        <source v-if="compSrc" :srcset="compSrc">
        <source :srcset="`${config.apiUrl}/storage/assets/nopreview.png`" type="image/png">
        <img :src="`${config.apiUrl}/storage/assets/nopreview.png`" class="ratio-image round" alt="thumbnail">
    </picture>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const props = defineProps<{
    src?: string,
    preferHq?: boolean,
    hasThumb?: boolean,
    urlPrefix: string,
}>();

const compSrc = computed(() => {
    if (!props.src || isSrcExternal(props.src)) {
        return props.src;
    } else {
        return `${config.apiUrl}/storage/${props.urlPrefix}/${(props.hasThumb && !props.preferHq) ? 'thumb_' : ''}${props.src}`;
    }
});
</script>