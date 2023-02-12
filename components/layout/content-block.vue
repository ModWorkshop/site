<template>
    <flex :gap="gap" :padding="padding" :column="column" :class="{'content-block': true, 'alt-content-bg': altBackground}">
        <slot/>
    </flex>
</template>

<script setup lang="ts">
defineProps({
    gap: {
        type: [Number, String],
        default: 3
    },
    column: {
        type: Boolean,
        default: true
    },
    padding: {
        type: [Number, String],
        default: 4
    },
    altBackground: Boolean
});
</script>