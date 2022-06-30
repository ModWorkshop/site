<template>
	<va-select 
		v-model="modelValue"
		@update:modelValue="update" 
		:placeholder="placeholder" 
		:options="fixedOptions" 
		:value-by="valueBy" 
		:text-by="textBy" 
		:multiple="multiple"
		:clearable="clearable"
		searchable
	/>
</template>
<script setup>
const props = defineProps({
	clearable: [String, Boolean],
	placeholder: String,
	options: Array,
	searchable: {type: [String, Boolean], default: true},
	textBy: {type: [String, Function], default: "name"},
	valueBy: {type: [String, Function], default: 'value'},
	modelValue: [Object, Number, String],
	multiple: Boolean,
});

const emit = defineEmits(['update', 'update:modelValue']);

function update(value) {
	emit('update:modelValue', value);
	emit('update', value);
}

const fixedOptions = computed(() => {
	const o = [];
	if (props.options != null) {
		props.options.forEach(item => {
			//VASelect does not like to use ID as value (value-by="id")
			o.push({
				value: item.id,
				...item,
			});
		});

		return o;
	}
	
	return props.options ?? o;
});

</script>