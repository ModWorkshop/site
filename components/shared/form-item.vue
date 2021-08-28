<template>
    <flex column gap="3">
        <label :for="uniqueId">{{label}}</label>
        <slot :label-id="uniqueId"/>
    </flex>
</template>

<script>
    export default {
        props: {
            label: String,
            labelId: String
        },
        computed: {
            uniqueId() {
                return `label__${this.labelId}`;
            }
        }
    }
</script>