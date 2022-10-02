<template>
    <flex class="input-container" inline column gap="2">
        <label v-if="label">
            {{label}}
        </label>
		<va-select 
			v-model="modelValue"
			:placeholder="placeholder" 
			:options="options" 
			:value-by="valueBy" 
			:track-by="valueBy"
			:text-by="textBy" 
			:multiple="multiple"
			:clearable="(clearable as boolean)"
			v-bind="$attrs"
			searchable
			@update:model-value="update"
		>
			<template #content>
				<slot name="content"/>
			</template>
		</va-select>
        <small v-if="desc">{{desc}}</small>
	</flex>
</template>
<script setup lang="ts">

defineProps({
	label: String,
	clearable: [String, Boolean],
	placeholder: String,
	options: Array,
	desc: String,
	searchable: {type: [String, Boolean], default: true},
	textBy: {type: [String, Function], default: "name"},
	valueBy: {type: String, default: 'id'},
	modelValue: [Object, Number, String],
	multiple: Boolean,
});

const emit = defineEmits(['update', 'update:modelValue']);

function update(value) {
	emit('update:modelValue', value);
	emit('update', value);
}
</script>