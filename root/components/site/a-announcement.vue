<template>
    <NuxtLink :to="`/thread/${thread.id}`" class="no-hover announcement">
        <content-block :column="false" padding="4" gap="0">
            <div>
                <h3>📢 {{thread.name}}</h3>
                <span class="ml-2">{{thread.content}}</span>
            </div>
            <i-mdi-close class="ml-auto mb-auto" @click.prevent="$emit('hide', thread)"/>
        </content-block>
    </NuxtLink>
</template>

<script setup lang="ts">
import { Thread } from '~~/types/models';

defineProps<{
    thread: Thread
}>();

defineEmits<{
    ( e: 'hide', thread: Thread ): void
}>();
</script>

<style scoped>
.announcement {
    flex: 1;
}

.announcement div {
    height: 100%;
}
</style>