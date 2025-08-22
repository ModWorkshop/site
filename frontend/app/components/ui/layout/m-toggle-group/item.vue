<template>
	<component
		:is="computedComponent"
		:selected="isSelected"
		v-bind="$attrs"
		:icon="icon"
		:color="buttonColor"
		@click="setSelected?.(value)"
	>
		<slot/>
	</component>
</template>

<script setup lang="ts">
const props = defineProps<{
	value: any;
	icon?: string;
}>();

const buttonStyle = inject<string | undefined>('buttonStyle', 'button');
const selected = inject<Ref<string>>('selected');
const setSelected = inject<(name) => void>('setSelected');

const isSelected = computed(() => props.value == selected?.value);

const buttonColor = computed(() => {
	if (buttonStyle == 'button') {
		return isSelected.value ? 'primary' : 'secondary';
	}
});

const computedComponent = computed(() => {
	if (!buttonStyle || buttonStyle == 'nav') {
		return resolveComponent('MNavLink');
	} else if (buttonStyle == 'button') {
		return resolveComponent('MButton');
	} else if (buttonStyle == 'dropdown') {
		return resolveComponent('MDropdownItem');
	}
});
</script>
