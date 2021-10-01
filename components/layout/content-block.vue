<template>
    <flex :gap="gap" column class="content-block p-2">
        <slot/>
    </flex>
</template>

<script setup>
    defineProps({
        gap: {
            type: Number,
            default: 3
        },
    });
</script>

<style>
.content-block {
    background-color: var(--secondary-bg-color);
    border-radius: 4px;
}
</style>