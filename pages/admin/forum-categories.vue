<template>
    <a-list url="forum-categories" :item-link="item => `/admin/forum-categories/${item.id}`" new-button="/admin/forum-categories/new"/>
</template>

<script setup>
</script>