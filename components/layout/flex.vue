<template>
    <div :class="{
        'flexbox': !inline,
        'inline-flex': inline, 
        'flex-col': column,
        'flex-wrap': wrap,
        [`gap-${gap}`]: !!gap,
        'flex-grow': grow
    }"><slot/></div>
</template>
<script setup>
    defineProps({
        inline: Boolean,
        column: Boolean,
        wrap: Boolean,
        gap: [Number, String],
        grow: Boolean,
    });
</script>