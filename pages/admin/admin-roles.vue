<template>
    <a-searchable-list url="roles" :item-link="item => `/admin/roles/${item.id}`" new-button="/admin/roles/new">
        <template #item="{ item }">
            <template v-if="item.id == 1">
                <br>
                <small>
                    All members have this role
                </small>
            </template>
        </template>
    </a-searchable-list>
</template>

<script setup>
</script>