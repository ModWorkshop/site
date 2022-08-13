<template>
    <table class="table">
        <thead>
            <tr>
                <slot name="head"/>
            </tr>
        </thead>
        <tbody>
            <slot/>
        </tbody>
    </table>
</template>

<style scoped>
.table {
    width: 100%;
    background-color: var(--alt-bg-color);
    border-radius: var(--border-radius);
    padding: 1rem;
}
</style>