<template>
    <flex column class="table-container">
        <table :class="{table: true, 'table-bg': background}">
            <slot/>
        </table>
    </flex>
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
.table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-radius: var(--border-radius);
    border-spacing: 0rem 0.25rem;
    white-space: nowrap;
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