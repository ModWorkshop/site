<template>
    <NuxtLink v-if="toOrHref && !disabled" :to="toOrHref" :class="clss" :download="download">
        <font-awesome-icon v-if="icon" :icon="icon" :size="iconSize" :style="iconStyle"/> <slot/>
    </NuxtLink>
    <button v-else :disabled="disabled" :class="clss" :type="type"> 
        <font-awesome-icon v-if="icon" :icon="icon" :size="iconSize" :style="iconStyle"/> <slot/>
    </button>
</template>

<script setup>
    const props = defineProps({
        href: String,
        large: Boolean,
        unstyled: Boolean,
        color: {
            default: 'primary',
            type: String,
        },
        type: {
            default: 'button',
            type: String
        },
        noBg: Boolean,
        download: {
            default: undefined,
            type: [String, Boolean]
        },
        to: String,
        iconSize: String,
        icon: [String, Array],
        iconRotation: Number,
        disabled: Boolean
    });

    const toOrHref = computed(() => props.to || props.href);

    const clss = computed(() => ({
        button: !props.unstyled,
        [`button-${props.color}`]: !props.unstyled,
        'button-no-bg': props.noBg,
        'button-large': props.large,
        'cursor-pointer': props.unstyled
    }));

    const iconStyle = computed(() => ({
        transform: props.iconRotation ? `Rotate(${props.iconRotation}deg)` : null
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

    .button-subtle {
        background-color: transparent;
    }

    .button-subtle:hover {
        background-color: var(--tab-selected-color);
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

    .button:hover:enabled, .button:hover:link {
        color: var(--button-text-color);
        transition: 0.15s ease-in-out;
        transition-property: background-color, color, border-color;
        cursor: pointer;
    }

    .button-primary:hover:enabled, a.button-primary:hover  {
        background-color: var(--primary-hover-color);
    }

    .button-danger:hover:enabled {
        background-color: var(--danger-hover-color);
    }
</style>