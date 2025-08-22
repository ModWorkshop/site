<template>
	<m-select
		v-model="vm"
		url="users"
		:placeholder="placeholder"
		@update:model-value="value => $emit('update:modelValue', value)"
	>
		<template #list-option="{ option }">
			<a-user :user="option" avatar-size="xs" static show-at/>
		</template>
		<template #option="{ option }">
			<a-user :user="option" avatar-size="xs" static show-at/>
		</template>
	</m-select>
</template>

<script setup lang="ts">
import type { User } from '~/types/models';

const props = defineProps<{
	modelValue?: number | User | Array<User> | null;
	placeholder?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const vm = useVModel(props, 'modelValue', emit);
</script>
