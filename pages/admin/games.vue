<template>
    <m-list url="games" query :item-link="item => `/g/${item.id}/admin`" new-button="/g/new">
        <template #before-item="{ item }">
            <game-thumbnail :src="item.thumbnail" style="width: 100px;"/>
        </template>
    </m-list>
</template>

<script setup>
useNeedsPermission('manage-games');
</script>