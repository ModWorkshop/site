<template>
    <a-list url="roles" :item-link="item => `/admin/roles/${item.id}`" new-button="/admin/roles/new">
        <template #item-name="{ item }">
            <template v-if="item.id == 1">
                <br>
                <small>
                    All members have this role
                </small>
            </template>
        </template>
    </a-list>
</template>

<script setup>
</script>