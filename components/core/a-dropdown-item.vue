<template>
    <nuxt-link v-if="to" class="dropdown-item cursor-pointer" :to="to"><slot/></nuxt-link>
    <a v-else-if="click" class="dropdown-item cursor-pointer" @click="click"><slot/></a>
</template>

<script setup>
    defineProps({
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

    .dropdown-item {
        color: var(--text-color);
        font-size: 0.9rem;
        text-align: left;
        padding: 0.65rem 1.5rem;
        display: block;
    }

    .dropdown-item:hover {
        background: var(--dropdown-hover-bg);
    }
</style>