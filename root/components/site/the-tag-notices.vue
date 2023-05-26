<template>
    <a-alert v-for="[noticeType, notices] of Object.entries(sortedNotices)" :key="noticeType" :color="noticeType">
        <flex v-if="notices.length > 1" column class="ml-4 mt-2" gap="2">
            <li v-for="notice in notices" :key="notice.id">
                {{ notice.localized ? $t(notice.notice) : notice.notice }}
            </li>
        </flex>
        <span v-else>
            {{ notices[0].localized ? $t(notices[0].notice) : notices[0].notice }}
        </span>
    </a-alert>
</template>

<script setup lang="ts">
import { Tag } from '~~/types/models';

const props = defineProps<{
    tags: Tag[]
}>();

const sortedNotices = computed(() => {
    interface TagNotice {
        id: number;
        type: string;
        notice: string;
        localized: boolean;
    }

    const notices: { [type: string]: TagNotice[] } = {};
    for (const tag of props.tags) {
        if (tag.notice && tag.notice.length > 0) {
            notices[tag.notice_type] ??= [];
            notices[tag.notice_type].push({ id: tag.id, type: tag.notice_type, notice: tag.notice, localized: tag.notice_localized });
        }
    }

    return notices;
});


</script>