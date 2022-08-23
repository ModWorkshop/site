<template>
    <div :class="classes">
        <slot/>
    </div>
</template>
<script setup>
const props = defineProps({
    inline: Boolean,
    column: Boolean,
    wrap: Boolean,
    gap: {type: [Number, String], default: 1},
    padding: [Number, String],
    grow: Boolean,
});

const classes = computed(() => {
    const c = {
        flexbox: !props.inline,
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

</script>