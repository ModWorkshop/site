<template>
	<m-flex gap="2" class="breadcrumb md:p-0">
		<template v-for="(item, i) in items" :key="`${item.name}-${i}`">
			<m-breadcrumb-link :item="item" :items="items" :last="i == (items.length - 1)"/>
			<span v-if="i != items.length - 1" :key="`split_${item.name}`"> <i-mdi-menu-right/> </span>
		</template>
	</m-flex>
</template>

<script setup lang="ts">
import type { Breadcrumb } from '~/types/models';

defineProps<{
	items: Breadcrumb[];
}>();
</script>

<style scoped>
.breadcrumb {
	color: var(--breadcrumb-secondary);
	width: 100%;
	padding: 0.5rem;
	font-weight: bold;
	overflow-x: auto;
}

.breadcrumb > * {
	white-space: nowrap;
}
</style>
