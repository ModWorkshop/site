<template>
	<textarea
		:id="labelId"
		ref="element"
		v-model="vm"
		type="textarea"
		:class="{ textarea: true, 'input-error': !!err, 'extra-round': true, [`p-${padding}`]: padding > 0 }"
		:rows="rows"
		v-bind="$attrs"
	/>
</template>

<script setup lang="ts">
const { padding = 6, rows = 12 } = defineProps<{
	labelId?: string;
	padding?: number;
	rows?: string | number;
}>();

const vm = defineModel<string>();

const element = ref();
const err = inject<Ref<boolean>>('err');

defineExpose({
	element
});
</script>
