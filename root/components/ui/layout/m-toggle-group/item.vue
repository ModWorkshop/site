<template>
    <component :is="computedComponent" :selected="isSelected" v-bind="$attrs" :icon="icon" @click="setSelected?.(name)">
        <slot/>
    </component>
</template>

<script setup lang="ts">
const props = defineProps({
    name: [String, Number],
    icon: String,
});

const buttonStyle = inject<string|undefined>('buttonStyle', 'button');
const selected = inject<Ref<string>>('selected');
const setSelected = inject<(name) => void>('setSelected');

const isSelected = computed(() => props.name == selected?.value);

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