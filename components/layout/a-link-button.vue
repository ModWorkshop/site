<template>
    <NuxtLink class="link-button">
        <slot/>
    </NuxtLink>
</template>

<style scoped>
.link-button {
    color: var(--text-color);
}

.link-button:hover {
    cursor: pointer;
    color: var(--primary-hover-color);
}
</style>