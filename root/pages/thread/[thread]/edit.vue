<template>
    <edit-thread-page v-model="thread" :game="thread.game"/>
</template>

<script setup lang="ts">
import type { Thread } from '~/types/models';

const thread = defineModel<Thread>('thread', { required: true });
</script>