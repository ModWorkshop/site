<template>
    <page-block size="sm" :breadcrumb="breadcrumb">
        <edit-thread-page/>
    </page-block>
</template>

<script lang="ts" setup>
const { t } = useI18n();

definePageMeta({
    middleware: 'unbanned-users-only'
});

const breadcrumb = [
    {
        name: t('forum'),
        to: 'forum'
    },
    {
        name: t('post'),
        to: 'forum/post'
    }
];
</script>