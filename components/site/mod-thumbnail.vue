<template>
    <picture>
        <source v-if="src" :srcset="src">
        <source :srcset="`${config.apiUrl}/storage/assets/nopreview.png`" type="image/png">
        <img :src="`${config.apiUrl}/storage/assets/nopreview.png`" class="ratio-image round" alt="thumbnail">
    </picture>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

const { public: config } = useRuntimeConfig();

const props = defineProps<{
    mod: Mod,
    preferHq?: boolean
}>();

const src = computed(() => {
    const thumb = props.mod.thumbnail;
    if (thumb) {
        return `${config.apiUrl}/storage/mods/images/${(thumb.has_thumb && !props.preferHq) ? 'thumb_' : ''}${props.mod.thumbnail.file}`;
    } else {
        return null;
    }
});
</script>