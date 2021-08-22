<template>
    <div :class="{flex: true, 'flex-col': column, 'flex-wrap': wrap, [`gap-${gap}`]: !!gap, 'flex-grow': grow}"><slot/></div>
</template>
<script>
    export default {
        props: {
            column: Boolean,
            wrap: Boolean,
            gap: [Number, String],
            grow: Boolean,
        }
    }
</script>