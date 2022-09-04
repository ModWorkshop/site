<template>
    <flex 
        v-show="show"
        role="tabpanel"
        :aria-hidden="!show"
        :aria-labelledby="`${name}-tab-link`"
        tabindex="0"
        class="tab-panel"
        column
        :gap="gap"
	>
        <slot/>
    </flex>
</template>

<script setup>
const props = defineProps({
    name: String,
    title: String,
    gap: {
        type: [String, Number],
        default: 4
    }
});

const tabState = inject('tabState');
const show = computed(() => props.name == tabState.current);
</script>