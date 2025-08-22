<template>
	<NuxtLink :class="{ 'dropdown-item': true, disabled }" :to="!disabled ? to : undefined" @click="onClick">
		<m-icon v-if="icon" :icon="icon" :size="iconSize"/> <slot/>
	</NuxtLink>
</template>

<script setup lang="ts">
const { disabled } = defineProps<{
	iconSize?: string;
	command?: string | number | object;
	to?: string;
	href?: string;
	disabled?: boolean;
	type?: string;
	icon?: Component;
}>();

function onClick(e) {
	if (disabled) {
		e.preventDefault();
	}
}
</script>

<style>
.dropdown-splitter {
	height: 1px;
	margin: 6px;
	background: var(--secondary-text-color);
	opacity: 0.25;
}
</style>

<style scoped>
.dropdown-item {
	color: var(--text-color);
	min-width: 150px;
	text-align: left;
	padding: 0.75rem;
	border-radius: var(--border-radius);
	display: flex;
	gap: 4px;
	user-select: none;
	transition:
		background-color 0.1s ease-in-out,
		color 0.1s ease-in-out;
}

.dropdown-item[selected='true'] {
	background: var(--dropdown-selected-bg);
}

.dropdown-item:not(.disabled) {
	cursor: pointer;
}

.dropdown-item:hover:not(.disabled) {
	background: var(--dropdown-hover-bg);
	color: var(--text-color);
}

.dropdown-item.disabled {
	opacity: 0.4;
}
</style>
