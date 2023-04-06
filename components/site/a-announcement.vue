<template>
    <NuxtLink :to="`/thread/${thread.id}`" class="no-hover flex-grow">
        <content-block :column="false" padding="4" gap="0">
            <div>
                <h3> {{thread.name}}</h3>
                <span class="ml-2">{{thread.content}}</span>
            </div>
            <a-icon class="ml-auto mb-auto" icon="xmark" @click.prevent="$emit('hide', thread)"/>
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

</style>