<template>
    <page-block>
        <mod-list/>
    </page-block>
</template>

<script setup nuxt>
    import { useStore } from '../store';
    import { storeToRefs } from 'pinia';

    const store = useStore();
    const { counter } = storeToRefs(store)
    
    function onPressed() {
        counter.value++;
    }
</script>