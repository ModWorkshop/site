<template>
    <edit-thread-page v-model="thread" :game="thread.game"/>
</template>

<script setup lang="ts">
const thread = defineModel('thread', { required: true });
</script>