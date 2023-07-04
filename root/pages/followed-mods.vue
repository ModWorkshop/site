<template>
    <page-block>
        <mod-list side-filters query url="followed-mods"/>
    </page-block>
</template>

<script setup>
definePageMeta({
    middleware: 'users-only',
});
</script>