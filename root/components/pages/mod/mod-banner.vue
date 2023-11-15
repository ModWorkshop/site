<template>
    <a-banner class="mod-banner" :src="mod.legacy_banner_url || (mod.banner && mod.banner.file)" height="170" url-prefix="mods/images"/>
</template>

<script setup lang="ts">
import type { Mod } from '~~/types/models';
defineProps<{
    mod: Mod,
    static?: boolean
}>();
</script>