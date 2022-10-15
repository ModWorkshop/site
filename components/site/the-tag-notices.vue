<template>
    <a-alert v-for="notice of notices" :key="notice.id" :color="notice.type" :desc="notice.localized ? $t(notice.notice) : notice.notice"/>
</template>

<script setup lang="ts">
import { Tag } from '~~/types/models';

const props = defineProps<{
    tags: Tag[]
}>();
const notices = computed(() => {
    const notices: { id: number, type: string, notice: string, localized: boolean }[] = [];
    for (const tag of props.tags) {
        if (tag.notice && tag.notice.length > 0 && notices.length < 2) {
            notices.push({ id: tag.id, type: tag.notice_type, notice: tag.notice, localized: tag.notice_localized });
        }
    }

    return notices;
});
</script>