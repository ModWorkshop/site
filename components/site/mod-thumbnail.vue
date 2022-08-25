<template>
    <a-thumbnail :src="thumbnail?.file" :has-thumb="thumbnail?.has_thumb" url-prefix="mods/images" :prefer-hq="preferHq"/>
</template>

<script setup lang="ts">
import { Image } from '~~/types/models';

defineProps<{
    thumbnail: Image,
    preferHq?: boolean
}>();
</script>