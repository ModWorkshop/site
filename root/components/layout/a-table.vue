<template>
    <flex column class="table-container">
        <table :class="{table: true, 'table-bg': background, 'alt-table-bg': altBackground}">
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
    },
    altBackground: {
        type: Boolean,
        default: false
    }
});
</script>

<style scoped>
.table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
}
</style>

<style>
.markdown table, .table {
    border-radius: var(--border-radius);
    border-spacing: 0rem 0.25rem;
    white-space: nowrap;
}

.markdown td, .table td {
    padding: 0.5rem 1.5rem;
}

.markdown th, .table th {
    padding: 1rem;
}

.markdown th, .table-bg th {
    background-color: var(--table-even-color);
}

.markdown th, .alt-table-bg th {
    background-color: var(--alt-table-even-color);
}

.table-bg td {
    background-color: var(--content-bg-color);
}

.markdown table, .table {
    border-collapse: separate;
}

.table-bg tr:nth-child(2n+1) td {
    background-color: var(--table-odd-color);
}

.table-bg tr:nth-child(2n) td {
    background-color: var(--table-even-color);
}

.markdown tr:nth-child(2n) td, .alt-table-bg tr:nth-child(2n) td {
    background-color: var(--alt-table-even-color);
}

.markdown tr:nth-child(2n+1) td, .alt-table-bg tr:nth-child(2n+1) td {
    background-color: var(--alt-table-odd-color);
}

.markdown .table tr:first-child td:first-child, .table tr:first-child td:first-child { 
    border-top-left-radius: var(--border-radius);
}

.markdown tr:first-child td:last-child, .table tr:first-child td:last-child {
    border-top-right-radius: var(--border-radius);
}

.markdown table tr:last-child td:first-child, .table tr:last-child td:first-child { 
    border-bottom-left-radius: var(--border-radius);
}
.markdown table tr:last-child td:last-child, .table tr:last-child td:last-child {
    border-bottom-right-radius: var(--border-radius);
}
</style>