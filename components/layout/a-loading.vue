<template>
    <flex>
        <font-awesome-icon :class="['loading', 'mx-auto', 'text-'+color, 'text-'+size]" icon="circle-notch"/>
    </flex>
</template>

<script setup>
defineProps({
    color: {
        type: String,
        default: 'primary',
    },
    size: {
        type: String,
        default: '3xl'
    }
});
</script>

<style scoped>
.loading {
    animation-name: rotatingLoading;
    animation-duration: 1s;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}
</style>

<style>
@keyframes rotatingLoading {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>