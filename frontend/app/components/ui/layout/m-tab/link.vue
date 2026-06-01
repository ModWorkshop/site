<template>
	<a
		:id="`${tabName}-tab-link`"
		role="tab"
		:class="{ 'tab-link': true, 'tab-link-side': side, selected: tabState.current == tabName }"
		:data-selected="tabState.current == tabName"
		:tabindex="tabState.current == tabName ? 0 : -1"
		:aria-selected="tabState.current == tabName"
		:aria-controls="`${tabName}-tab-panel`"
	>
		<m-icon v-if="icon" :icon="icon" :size="iconSize"/> {{ tabTitle }}
	</a>
</template>

<script setup>
defineProps({
	tabTitle: String,
	tabName: String,
	icon: String
});

const tabState = inject('tabState');
const side = inject('side', false);
</script>

<style>
.tab-link {
	font-size: 1.125rem;
	border-radius: var(--content-border-radius);
	padding: 0.75rem 2rem;
	text-align: center;
	color: var(--secondary-text-color);
	transition: ease-in-out 0.15s;
	transition-property: color, background-color;
}

.tab-link:hover {
	cursor: pointer;
	color: var(--primary-color);
}

.tab-link.selected {
	color: var(--primary-color);
	background-color: var(--tab-selected-color);
}

.tab-link-side {
	text-align: left;
	padding: 0.75rem;
	min-width: 200px;
}
</style>
