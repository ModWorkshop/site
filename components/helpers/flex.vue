<template>
    <div :style="{flexDirection: column && 'column'}" class="d-flex"><slot/></div>
</template>
<script>
    export default {
        props: {
            column: Boolean
        }
    }
</script>
<style scoped>
    .d-flex {
        display: flex;
    }
</style>