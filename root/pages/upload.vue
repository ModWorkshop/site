<template>
    <page-block>
        <upload-page/>
    </page-block>
</template>

<script lang="ts" setup>
definePageMeta({
    middleware: 'unbanned-users-only'
});
</script>