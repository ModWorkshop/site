<template>
    <div v-show="name == tabState.current">
        <slot/>
    </div>
</template>

<script>
    export default {
        props: {
            name: String,
            title: String
        },
        inject: ['tabState']
    }
</script>