<template>
    <page-block>
        <a-button @click="increment">beepeee!!!</a-button>
        Counter: {{store.counter}}
        <mod-list/>
    </page-block>
</template>

<script setup>
    import { useStore } from '~~/store';

    const store = useStore();

    function increment() {
        store.counter++;
    }
</script>