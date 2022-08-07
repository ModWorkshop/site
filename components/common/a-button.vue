<template>
    <NuxtLink v-if="download && !disabled" :href="href" :class="clss" :download="download">
        <font-awesome-icon v-if="icon" :icon="icon" :size="iconSize"/> <slot/>
    </NuxtLink>
    <nuxt-link v-else-if="to && !disabled" :to="to || href" :class="clss">
        <font-awesome-icon v-if="icon" :icon="icon" :size="iconSize"/> <slot/>
    </nuxt-link>
    <button v-else :disabled="disabled" :class="clss" :type="type"> 
        <font-awesome-icon v-if="icon" :icon="icon" :size="iconSize"/> <slot/>
    </button>
</template>

<script setup>
    const props = defineProps({
        href: String,
        large: Boolean,
        color: {
            default: 'primary',
            type: String,
        },
        type: {
            default: 'button',
            type: String
        },
        noBg: Boolean,
        download: [String, Boolean],
        to: String,
        iconSize: String,
        icon: [String, Array],
        disabled: Boolean
    });

    const clss = computed(() => ({
        button: true,
        [`button-${props.color}`]: true,
        'button-no-bg': props.noBg,
        'button-large': props.large
    }));
</script>

<style scoped>
    .button {
        color: var(--button-text-color);
        padding: 0.5rem 0.75rem;
        border: 1px solid transparent;
        border-radius: var(--border-radius);
        transition: 0.15s ease-in-out;
        transition-property: background-color, color, border-color;
    }

    .button-none {
        background-color: transparent;
    }

    .button-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .button-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }

    .button-no-bg {
        background-color: transparent;
        color: var(--primary-color);
    }

    .button-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .button-large {
        font-size: 1.5rem;
    }

    .button:disabled {
        background-color: var(--disabled-color);
        border-color: var(--disabled-color);
        opacity: 0.75;
    }

    .button:hover:enabled {
        color: var(--button-text-color);
        transition: 0.15s ease-in-out;
        transition-property: background-color, color, border-color;
        cursor: pointer;
    }

    .button-primary:hover:enabled {
        background-color: var(--primary-hover-color);
    }

    .button-danger:hover:enabled {
        background-color: var(--danger-hover-color);
    }
</style>