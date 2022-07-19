<template>
    <a-searchable-list url="tags" :item-link="item => `/admin/tags/${item.id}`" new-button="/admin/tags/new"/>
</template>

<script setup>
</script>