<template>
    <a-list url="users" query :item-link="item => `/user/${item.id}/edit`">
        <template #item="{ item }">
            <a-list-item :to="`/user/${item.id}`">
                <a-user :user="item" static show-at/>
            </a-list-item>
        </template>
    </a-list>
</template>

<script setup>
useNeedsPermission('manage-users');
</script>