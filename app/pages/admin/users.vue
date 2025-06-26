<template>
    <user-list :user-link="item => `/user/${item.id}/edit`" column alt-background/>
</template>

<script setup>
useNeedsPermission('manage-users');
</script>