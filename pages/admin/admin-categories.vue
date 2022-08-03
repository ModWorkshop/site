<template>
    <a-list url="categories" :item-link="item => `/admin/categories/${item.id}`" new-button="/admin/categories/new"/>
</template>

<script setup>
</script>