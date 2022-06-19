<template>
    <a-tab :name="name" :title="title" class="flex flex-col gap-3">
        <slot/>
    </a-tab>
</template>

<script setup>
    defineProps({
        name: String,
        title: String
    });
</script>