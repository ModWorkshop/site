<template>
    <page-block size="md">
        <content-block class="p-6">
            <a-nav side root="/admin">
                <a-nav-link to="" title="Home"/>
                <a-nav-link to="roles" title="Roles"/>
                <a-nav-link to="tags" title="Tags"/>
                <a-nav-link to="users" title="Users"/>
                <a-nav-link to="cases" title="Cases"/>
                <a-nav-link to="docs" title="Documents"/>
                <a-nav-link to="reports" title="Reports"/>
                <a-nav-link to="settings" title="Settings"/>
                <a-nav-link to="forum-categories" title="Global Forum Categories"/>
                <a-nav-link to="games" title="Games"/>
                <a-nav-link to="mods" title="Mods"/>
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
});
</script>