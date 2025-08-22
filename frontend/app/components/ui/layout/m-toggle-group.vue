<template>
	<m-flex wrap>
		<slot/>
	</m-flex>
</template>

<script setup lang="ts">
const props = defineProps({
	selected: [String, Number],
	buttonStyle: String
});

const selected = toRef(props, 'selected');

const emit = defineEmits([
	'update:selected'
]);

provide('selected', selected);
provide('setSelected', name => emit('update:selected', name));
provide('buttonStyle', props.buttonStyle);
</script>
