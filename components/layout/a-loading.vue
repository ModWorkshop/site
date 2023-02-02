<template>
    <flex>
        <Icon :class="['loading', 'mx-auto', color && 'text-'+color, 'text-'+size]" name="svg-spinners:pulse-2"/>
    </flex>
</template>

<script setup>
defineProps({
    color: String,
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