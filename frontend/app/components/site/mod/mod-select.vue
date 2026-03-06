<template>
	<m-select v-model="vm" :url="url">
		<template #list-option="{ option }">
			<search-list-mod :key="option.id" :mod="option" static :background="false"/>
		</template>
	</m-select>
</template>

<script setup lang="ts" generic="T">
const { url = 'mods' } = defineProps<{
	url?: string;
}>();

const vm = defineModel<T>();
</script>
