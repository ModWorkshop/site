<template>
    <span class="tag"><slot/></span>
</template>

<style scoped>
    .tag {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        background: var(--primary-color);
        border-radius: var(--border-radius);
    }
</style>