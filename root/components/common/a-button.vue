<template>
    <NuxtLink v-if="toOrHref && !isDisabled" :to="toOrHref" :class="clss" :download="download">
        <span>
            <Icon v-if="loading" class="loading text-xl ml-2" name="svg-spinners:pulse-2"/>
            <a-icon v-if="icon" :name="icon" :size="iconSize" :style="iconStyle"/> <slot/>
        </span>
    </NuxtLink>
    <button v-else :disabled="isDisabled" :class="clss" :type="type"> 
        <span>
            <Icon v-if="loading" class="loading text-xl ml-2" name="svg-spinners:pulse-2"/>
            <a-icon v-if="icon" :name="icon" :size="iconSize" :style="iconStyle"/> <slot/>
        </span>
    </button>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    href?: string,
    to?: string,
    unstyled?: boolean,
    loading?: boolean,
    color?: 'primary' | 'warning' | 'subtle' | 'secondary' | 'danger' | 'none',
    type?: 'button' | 'submit' | 'reset',
    size?: string,
    noBg?: boolean,
    download?: string|boolean,
    iconSize?: string,
    icon?: string | number,
    iconRotation?: number,
    disabled?: boolean
}>(), {
    color: 'primary',
    type: 'button',
    download: undefined
});

const toOrHref = computed(() => props.to || props.href);

const clss = computed(() => ({
    button: !props.unstyled,
    [`button-${props.color}`]: !props.unstyled,
    'button-no-bg': props.noBg,
    'button-sm': props.size == 'sm',
    'button-xs': props.size == 'xs',
    'cursor-pointer': props.unstyled
}));

const iconStyle = computed(() => ({
    transform: props.iconRotation ? `Rotate(${props.iconRotation}deg)` : null
}));

const isDisabled = computed(() => props.disabled || props.loading);
</script>

<style scoped>
.button {
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0.75rem;
    border: 1px solid transparent;
    border-radius: var(--border-radius);
    transition: 0.15s ease-in-out;
    transition-property: background, color, border-color;
}

.button:not([disabled]):active {
    transform: scale(.98);
}

.button-sm {
    padding: 0.3rem 0.6rem;
}

.button-xs {
    padding: 0.2rem 0.4rem;
}

.button-none {
    background-color: transparent;
    color: var(--text-color);
}

.button-none:hover {
    color: var(--primary-color) !important;
}

.button-primary {
    color: var(--primary-color-text);
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.button-warning {
    color: black;
    background: var(--warning-color);
    border-color: var(--warning-color);
}

.button-warning:hover {
    color: black !important;
    background: var(--warning-hover-color);
}

.button-subtle {
    background-color: transparent;
    color: var(--text-color);
}

.button-subtle:hover {
    background: var(--tab-selected-color);
    color: var(--text-color) !important;
}

.button-danger {
    background: var(--danger-color);
    border-color: var(--danger-color);
}

.button-no-bg {
    background-color: transparent;
    color: var(--primary-color);
}

.button-secondary {
    background: var(--secondary-color);
    border-color: var(--secondary-color);
}

.button:disabled {
    background: var(--disabled-color);
    border-color: var(--disabled-color);
    opacity: 0.75;
}

.button:disabled:hover {
    cursor: not-allowed;
}

.button:hover:enabled, .button:hover:link {
    color: var(--button-text-color);
    transition: 0.15s ease-in-out;
    transition-property: background, color, border-color;
    cursor: pointer;
}

.button-primary:hover:enabled, a.button-primary:hover  {
    color: var(--primary-color-text);
    background: var(--primary-hover-color);
}

.button-danger:hover:enabled, a.button-danger:hover {
    background: var(--danger-hover-color);
}

.button-secondary:hover:enabled, a.button-secondary:hover {
    background: var(--secondary-hover-color);
}
</style>