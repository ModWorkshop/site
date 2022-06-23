<template>
    <a v-if="href && !disabled" :href="href" :class="clss" :download="download">
        <font-awesome-icon v-if="icon" :icon="icon" :size="iconSize"/> <slot/>
    </a>
    <nuxt-link v-else-if="to && !disabled" :to="to" :class="clss">
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
        download: [String, Boolean],
        to: String,
        iconSize: String,
        icon: [String, Array],
        disabled: Boolean
    });

    const clss = ref({
        button: true,
        [`button-${props.color}`]: true,
        'button-large': props.large
    });
</script>

<style scoped>
    .button {
        color: var(--button-text-color);
        padding: 0.5rem 0.75rem;
        border-radius: var(--border-radius);
        transition: background-color 0.5s;
    }

    .button-none {
        background-color: transparent;
    }

    .button-primary {
        background-color: var(--primary-color);
    }

    .button-secondary {
        background-color: var(--secondary-color);
    }


    .button-large {
        font-size: 1rem;
    }

    .button:disabled {
        background-color: var(--disabled-color);
    }

    .button:hover:enabled {
        transition: background-color 0.5s;
        cursor: pointer;
    }

    .button-primary:hover:enabled {
        background-color: var(--primary-hover-color);
        cursor: pointer;
    }
</style>