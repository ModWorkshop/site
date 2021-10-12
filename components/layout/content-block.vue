<template>
    <flex :gap="gap" column :class="{'content-block': true, 'alt-content-bg': altBackground, 'p-2': true}">
        <slot/>
    </flex>
</template>

<script setup>
    defineProps({
        gap: {
            type: Number,
            default: 3
        },
        altBackground: Boolean
    });
</script>

<style>
.content-block {
    background-color: var(--secondary-bg-color);
    border-radius: 4px;
}

.content-bg {
    background-color: var(--secondary-bg-color);
}

.alt-content-bg {
    background-color: var(--alt-bg-color);
}
</style>