<template>
	<m-select v-model="vm" url="games">
		<template #any-option="{ option }">
			<simple-game :game="option"/>
		</template>
	</m-select>
</template>

<script setup lang="ts">
const props = defineProps<{
	modelValue?: number;
}>();

const emit = defineEmits(['update:modelValue']);
const vm = useVModel(props, 'modelValue', emit);
</script>
