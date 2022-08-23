<template>
    <a-list url="categories" :item-link="item => `/admin/categories/${item.id}`" new-button="/admin/categories/new">
        <template #item="{ item, items }">
            <category-tree :category="item" :categories="items.data"/>
        </template>
    </a-list>
</template>

<script setup>
</script>