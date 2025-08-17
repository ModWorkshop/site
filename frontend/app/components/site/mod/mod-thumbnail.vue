<template>
    <a-thumbnail 
        class="mod-thumbnail"
        :src="thumbnail?.file"
        :lazy="lazy"
        :has-thumb="thumbnail?.has_thumb"
        url-prefix="mods/images"
        :prefer-hq="preferHq"
    />
</template>

<script setup lang="ts">
import type { Image } from '~/types/models';

defineProps<{
    thumbnail?: Image,
    lazy?: boolean,
    preferHq?: boolean
}>();
</script>