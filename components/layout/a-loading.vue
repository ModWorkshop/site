<template>
    <flex>
        <font-awesome-icon class="loading mx-auto" icon="circle-notch"/>
    </flex>
</template>

<style scoped>
.loading {
    color: var(--primary-color);
    font-size: 2rem;
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