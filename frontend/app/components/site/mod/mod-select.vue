<template>
	<m-select v-model="vm" :url="url" :fetch-params="fetchParams">
		<template #list-option="{ option }">
			<search-list-mod :key="option.id" :mod="option" static :background="false"/>
		</template>
	</m-select>
</template>

<script setup lang="ts" generic="T">
const { url = 'mods' } = defineProps<{
	url?: string;
	fetchParams?: Record<string, any>;
}>();

const vm = defineModel<T>();
</script>
