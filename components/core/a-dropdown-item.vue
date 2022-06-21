<template>
    <div class="dropdown-item cursor-pointer">
        <nuxt-link v-if="to" :to="to"><slot/></nuxt-link>
        <a v-else-if="click" @click="click"><slot/></a>
    </div>
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
        font-size: 0.9rem;
        text-align: left;
        padding: 0.65rem 1.5rem;
        display: block;
    }

    .dropdown-item a {
        color: var(--text-color);
    }

    .dropdown-item:hover {
        background: var(--dropdown-hover-bg);
    }
</style>