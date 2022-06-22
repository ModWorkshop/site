<template>
	<va-select v-model="modelValue" @update:modelValue="value => $emit('update:modelValue', value)" :placeholder="placeholder" :options="options" :value-by="valueBy" :text-by="textBy" :multiple="multiple" searchable/>
</template>
<script setup>
const { options } = defineProps({
	placeholder: String,
	options: Array,
	searchable: {type: [String, Boolean], default: true},
	textBy: {type: [String, Function], default: "name"},
	valueBy: {type: [String, Function], default: 'value'},
	modelValue: [Object, Number, String],
	multiple: Boolean,
});

onMounted(() => {
	if (options != null) {
		options.forEach(item => {
			//VASelect does not like to use ID as value (value-by="id")
			if (item.id && !item.value) {
				item.value = item.id;
			}
		});
	}
});

</script>