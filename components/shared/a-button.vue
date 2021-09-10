<template>
    <button class="button" @click="$emit('click', $event)"> 
        <font-awesome-icon v-if="icon" :icon="icon"/>
        <slot/>
    </button>
</template>

<script>
    export default {
        props: {
            icon: String
        }
    };
</script>

<style scoped>
    .button {
        background-color: var(--primary-color);
        color: var(--button-text-color);
        padding: 0.5rem 0.75rem;
        border-radius: var(--border-radius);
        transition: background-color 0.5s;
    }

    .button:hover {
        transition: background-color 0.5s;
        background-color: var(--primary-hover-color);
        cursor: pointer;
    }
</style>