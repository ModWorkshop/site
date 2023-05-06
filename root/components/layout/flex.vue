<template>
    <div ref="element" :class="classes">
        <slot/>
    </div>
</template>
<script setup lang="ts">
const props = defineProps({
    inline: Boolean,
    column: Boolean,
    wrap: Boolean,
    gap: {type: [Number, String], default: 1},
    padding: [Number, String, Boolean],
    grow: Boolean,
});

const classes = computed(() => {
    const c = {
        flex: !props.inline,
        'inline-flex': props.inline, 
        'flex-col': props.column,
        'flex-wrap': props.wrap,
        'flex-grow': props.grow,
    };

    if (props.gap) {
        c[`gap-${props.gap}`] = true;
    }

    if (props.padding) {
        c[`p-${props.padding}`] = true;
    }

    return c;
});


const element = ref<HTMLDivElement>();

defineExpose({element});
</script>