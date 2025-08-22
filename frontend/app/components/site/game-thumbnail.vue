<template>
	<a-thumbnail :src="src" :has-thumb="false" url-prefix="games/images"/>
</template>

<script setup lang="ts">
defineProps<{
	src?: string | Blob;
}>();
</script>
