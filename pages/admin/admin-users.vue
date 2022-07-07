<template>
    <a-searchable-list url="users" :item-link="item => `/admin/users/${item.id}`">
        <template #before-item="{ item }">
            <a-avatar :src="item.avatar || 'assets/nopreview.png'"/>
        </template>
        <template #item="{ item }">#{{item.id}}</template>
    </a-searchable-list>
</template>

<script setup>
</script>