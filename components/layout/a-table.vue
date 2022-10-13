<template>
    <table :class="{table: true, 'table-bg': background}">
        <slot/>
    </table>
</template>

<script setup>
defineProps({
    background: {
        type: Boolean,
        default: true
    }
});
</script>

<style scoped>
.table {
    width: 100%;
    border-radius: var(--border-radius);
}

.table-bg {
    background-color: var(--alt-content-bg-color);
}
</style>

<style>
.table td, .table th {
    padding: 0.5rem;
}
</style>