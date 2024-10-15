<template>
    <user-list :user-link="item => `/user/${item.id}/edit`" column/>
</template>

<script setup>
useNeedsPermission('manage-users');
</script>