<template>
    <div :class="{flex: true, 'flex-col': column, 'flex-wrap': wrap, [`gap-${gap}`]: !!gap}"><slot/></div>
</template>
<script>
    export default {
        props: {
            column: Boolean,
            wrap: Boolean,
            gap: Number
        }
    }
</script>