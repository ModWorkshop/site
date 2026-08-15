<template>
	<!--Lack of a better name-->
	<m-input :label="label">
		<m-flex column :class="classes">
			<m-flex>
				<m-input v-model="query" class="w-full" type="search"/>
				<slot name="buttons"/>
			</m-flex>
			<m-flex class="items" column gap="1">
				<div
					v-for="option of shownOptions"
					:key="option.id"
					:class="{
						option: true,
						'hover:cursor-pointer': true,
						'selected': optionSelected(option),
						'disallowed': optionDisallowed(option)
					}"
					@click.prevent="onClickOption(option, true)"
				>
					<strong>{{ option.name }}</strong>
					<m-button
						color="subtle"
						size="sm"
						class="disallow ml-auto opacity-0 hover:opacity-100"
						@click.stop="onClickOption(option, false)"
					>
						<i-mdi-block/>
					</m-button>
				</div>
			</m-flex>
		</m-flex>
	</m-input>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';

const { options, valueBy = 'id' } = defineProps<{
	label?: string;
	valueBy?: false | string | ((option) => string);
	options: any[];
}>();

const allowList = reactive<any[]>([]);
const disallowList = reactive<any[]>([]);

const allowListVm = defineModel<any[]>('allow');
const disallowListVm = defineModel<any[]>('disallow');
const classes = computed(() => ({ 'list': true, 'input-bg': true, 'p-2': true }));

const query = ref('');
const shownOptions = computed(() => options.filter(opt => opt.name.toLowerCase().match(query.value.toLowerCase())));

function optionValue(option) {
	if (typeof valueBy === 'function') {
		return valueBy(option);
	} else if (typeof valueBy === 'string') {
		return option[valueBy];
	} else {
		return option;
	}
}

function optionDisallowed(option) {
	const val = optionValue(option);

	return disallowList.includes(val);
}

function optionSelected(option) {
	const val = optionValue(option);

	return allowList.includes(val) || disallowList.includes(val);
}

function onClickOption(option, allow: boolean) {
	const val = optionValue(option);

	if (allowList.includes(val) || disallowList.includes(val)) {
		const removedAllowed = remove(allowList, val);
		const removedDisallowed = remove(disallowList, val);

		if ((allow && removedAllowed) || (!allow && removedDisallowed)) {
			allowListVm.value = allowList;
			disallowListVm.value = disallowList;

			return;
		}
	}
	if (allow) {
		allowList.unshift(val);
	} else {
		disallowList.unshift(val);
	}

	allowListVm.value = allowList;
	disallowListVm.value = disallowList;
}
</script>

<style scoped>

.list {
	overflow: hidden;
	max-height: 400px;
}

.items {
	overflow: auto;
	height: 100%;
	padding: 0 0.25rem 0 0;
}

.option {
	display: flex;
	align-items: center;
	padding: 0.5rem;
	flex: 1;
	border-radius: var(--border-radius);
	transition: 0.15s ease-in-out;
	transition-property: background-color, color, border-color;
}

.option.disallowed .disallow {
	color: var(--color-red-200) !important;
	opacity: 100%;
}

.option:hover {
	background-color: var(--tab-selected-color);
	transition: 0.15s ease-in-out;
	transition-property: background-color, color, border-color;
}

.option.disallowed {
	color: var(--color-red-200) !important;
	background-color: var(--danger-bg-color) !important;
}

.option.selected {
	background-color: var(--tab-selected-color);
	color: var(--primary-color);
}
</style>
