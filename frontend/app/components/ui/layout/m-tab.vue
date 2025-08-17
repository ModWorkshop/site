<template>
    <m-flex 
        v-show="show"
        role="tabpanel"
        :aria-hidden="!show"
        :aria-labelledby="`${name}-tab-link`"
        tabindex="0"
        class="tab-panel"
        column
        :gap="gap"
	>
        <slot v-if="renderedOnce || !lazy || show"/>
        <template v-else>
            <m-flex class="items-center justify-center h-full">
                <m-loading/>
            </m-flex>
        </template>
    </m-flex>
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
const lazy = inject('lazy');
const show = computed(() => props.name == tabState.current);

const renderedOnce = ref(false);

watch(show, () => renderedOnce.value = true);
</script>