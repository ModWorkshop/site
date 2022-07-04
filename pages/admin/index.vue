<template>
    <page-block size="med">
        <content-block class="p-6">
            <a-nav side>
                <a-nav-link to="/admin" title="Home"/>
                <a-nav-link to="/admin/roles" title="Roles"/>
                <a-nav-link to="/admin/tags" title="Tags"/>
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