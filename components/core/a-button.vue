<template>
    <a v-if="href && !disabled" :href="href" class="button">
        <font-awesome-icon v-if="icon" :icon="icon"/>
        <slot/>
    </a>
    <nuxt-link v-else-if="to && !disabled" :to="to" class="button">
        <font-awesome-icon v-if="icon" :icon="icon"/>
        <slot/>
    </nuxt-link>
    <button v-else :disabled="disabled" class="button" @click="$emit('click', $event)" :type="type"> 
        <font-awesome-icon v-if="icon" :icon="icon"/>
        <slot/>
    </button>
</template>

<script setup>
    defineProps({
        href: String,
        type: {
            default: 'button',
            type: String
        },
        to: String,
        icon: String,
        disabled: Boolean
    });
</script>

<style scoped>
    .button {
        background-color: var(--primary-color);
        color: var(--button-text-color);
        padding: 0.5rem 0.75rem;
        border-radius: var(--border-radius);
        transition: background-color 0.5s;
    }

    .button:disabled {
        background-color: var(--disabled-color);
    }

    .button:hover:enabled {
        transition: background-color 0.5s;
        background-color: var(--primary-hover-color);
        cursor: pointer;
    }

</style>