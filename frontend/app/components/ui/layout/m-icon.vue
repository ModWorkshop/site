<template>
	<span>
		<component :is="icon" :size="size"/>
	</span>
</template>

<script setup lang="ts">
defineProps<{
	icon: Component;
	size?: string;
}>();
</script>
