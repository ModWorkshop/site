<template>
    <page-block size="med">
        <content-block class="p-6">
            <a-nav side root="/admin">
                <a-nav-link to="/admin" title="Home"/>
                <a-nav-link to="/admin/roles" title="Roles"/>
                <a-nav-link to="/admin/tags" title="Tags"/>
                <a-nav-link to="/admin/users" title="Users"/>
                <a-nav-link to="/admin/categories" title="Categories"/>
                <a-nav-link to="/admin/games" title="Games"/>
                <template #content>
                    <NuxtPage/>
                </template>
            </a-nav>
        </content-block>
    </page-block>
</template>

<script setup>
definePageMeta({
    middleware: 'admins-only',
    keepalive: true,
});
</script>

<style scoped>

</style>