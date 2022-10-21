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

<style>
.content-block {
    max-width: 100%;
    background-color: var(--content-bg-color);
    border-radius: var(--border-radius);
}

.content-bg {
    background-color: var(--content-bg-color);
}

.alt-content-bg {
    background-color: var(--alt-content-bg-color);
}
</style>