<template>
    <page-block>
        <mod-list side-filters query url="mods/liked"/>
    </page-block>
</template>

<script setup>
definePageMeta({
    middleware: 'users-only',
});
</script>