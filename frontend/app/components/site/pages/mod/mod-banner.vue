<template>
	<m-banner class="mod-banner" :src="mod.legacy_banner_url || (mod.banner && mod.banner.file)" :height="false" url-prefix="mods/images"/>
</template>

<script setup lang="ts">
import type { Mod } from '~/types/models';
defineProps<{
	mod: Mod;
	static?: boolean;
}>();
</script>

<style>
.mod-banner {
	aspect-ratio: 4 / 1;
	height: initial;
}
</style>
