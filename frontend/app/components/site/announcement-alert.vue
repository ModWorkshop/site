<template>
    <div class="hover:cursor-pointer announcement" @click="$router.push(`/thread/${thread.id}`)">
        <m-content-block class="content-block-glass h-full" :column="false" padding="4" gap="0">
            <div>
                <span class="h2">📢 {{thread.name}}</span>
                <md-content 
                    class="ellipsis-overflow announcement-preview"
                    :padding="0"
                    style="-webkit-line-clamp: 2;"
                    :text="thread.content"
                    remove-tags
                     :parser-version="thread.parser_version"
                />
            </div>
            <i-mdi-close class="ml-auto mb-auto" @click.prevent="$emit('hide', thread)"/>
        </m-content-block>
    </div>
</template>

<script setup lang="ts">
import type { Thread } from '~/types/models';

defineProps<{
    thread: Thread
}>();

defineEmits<{
    ( e: 'hide', thread: Thread ): void
}>();
</script>

<style scoped>
.announcement {
    overflow: hidden;
    flex-grow: 1;
}

.announcement-preview {
    margin: 1.5rem 1rem 0 1rem;
    line-height: 1;
}
</style>