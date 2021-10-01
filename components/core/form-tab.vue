<template>
    <tab :name="name" :title="title" class="flex flex-col gap-3">
        <slot/>
    </tab>
</template>

<script setup>
    defineProps({
        name: String,
        title: String
    });
</script>