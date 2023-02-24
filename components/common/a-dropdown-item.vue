<template>
    <NuxtLink :class="{'dropdown-item': true, disabled}" :to="to" @click="click">
        <a-icon v-if="icon" :icon="icon" :size="iconSize"/> <slot/>
    </NuxtLink>
</template>

<script setup lang="ts">
defineProps({
    iconSize: String,
    command: [String, Number, Object],
    to: String,
    href: String,
    disabled: Boolean,
    type: String,
    icon: String
});

const emit = defineEmits(['click']);

function click() {
    emit('click');
}
</script>

<style>
.dropdown-splitter {
    height: 1px;
    margin: 6px;
    background: var(--secondary-text-color);
    opacity: 0.25;
}
</style>

<style scoped>
.dropdown-item {
    color: var(--text-color);
    text-align: left;
    padding: 0.65rem 1.5rem;
    display: flex;
    gap: 4px;
    user-select: none;
}

.dropdown-item:hover {
    color: var(--primary-hover-color);
    transition: color 0.15s ease-in-out;
}

.dropdown-item:not(.disabled) {
    cursor: pointer;
}

.dropdown-item:hover:not(.disabled) {
    background: var(--dropdown-hover-bg);
}
</style>