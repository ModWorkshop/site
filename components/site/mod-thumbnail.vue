<template>
    <picture>
        <source v-if="mod.thumbnail" :srcset="`${config.apiUrl}/storage/mods/images/${mod.thumbnail.file}`">
        <source :srcset="`${config.apiUrl}/storage/assets/nopreview.png`" type="image/png">
        <img :src="`${config.apiUrl}/storage/assets/nopreview.png`" class="ratio-image round" alt="thumbnail">
    </picture>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

const { public: config } = useRuntimeConfig();

defineProps<{
    mod: Mod
}>();
</script>