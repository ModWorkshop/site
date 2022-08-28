<template>
    <a-list url="users" :item-link="item => `/user/${item.id}/edit`">
        <template #before-item="{ item }">
            <a-avatar :src="item.avatar"/>
        </template>
        <template #item-item="{ item }">({{item.unique_name}}#{{item.id}})</template>
    </a-list>
</template>

<script setup>
</script>