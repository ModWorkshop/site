<template>
    <flex column class="table-container">
        <table :class="{table: true, 'table-bg': background}">
            <slot>
                <thead>
                    <tr>
                        <slot name="head"/>
                    </tr>
                </thead>
                <tbody>
                    <slot name="body"/>
                </tbody>
            </slot>
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

.table {
    border-collapse: separate;
    border-spacing: 0;
}

.table tr:first-child td:first-child { 
    border-top-left-radius: var(--border-radius);
}

.table tr:first-child td:last-child {
    border-top-right-radius: var(--border-radius);
}

.table tr:last-child td:first-child { 
    border-bottom-left-radius: var(--border-radius);
}
.table tr:last-child td:last-child {
    border-bottom-right-radius: var(--border-radius);
}
</style>