<template>
    <NuxtLink :to="`/thread/${thread.id}`" class="no-hover announcement">
        <content-block :column="false" padding="4" gap="0">
            <div>
                <span class="h2">📢 {{thread.name}}</span>
                <a-markdown class="ml-2" :text="thread.content"/>
            </div>
            <i-mdi-close class="ml-auto mb-auto" @click.prevent="$emit('hide', thread)"/>
        </content-block>
    </NuxtLink>
</template>

<script setup lang="ts">
import type { Thread } from '~~/types/models';

defineProps<{
    thread: Thread
}>();

defineEmits<{
    ( e: 'hide', thread: Thread ): void
}>();
</script>

<style scoped>
.announcement {
    height: 85px;
    overflow: hidden;
    flex-grow: 1;
}
</style>