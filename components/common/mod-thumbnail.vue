<template>
    <picture class="overflow-hidden">
        <source v-if="mod.thumbnail" :srcset="`http://localhost:8000/storage/mods/images/${mod.thumbnail.file}`">
        <source srcset="http://localhost:8000/storage/assets/nopreview.png" type="image/png">
        <img src="http://localhost:8000/storage/assets/nopreview.png" :class="{'ratio-image': true}" alt="thumbnail">
    </picture>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

defineProps<{
    mod: Mod
}>();
</script>