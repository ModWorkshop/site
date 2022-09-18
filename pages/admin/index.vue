<template>
    <page-block size="md">
        <content-block class="p-6">
            <a-nav side root="/admin">
                <h3>General</h3>
                <a-nav-link to="" title="Home"/>
                <a-nav-link to="settings" title="Settings"/>
                <a-nav-link to="roles" title="Roles"/>
                <h3>Moderation</h3>
                <a-nav-link to="cases" title="Cases"/>
                <a-nav-link to="approvals" title="Approvals"/>
                <a-nav-link to="bans" title="Bans"/>
                <a-nav-link to="suspensions" title="Suspensions"/>
                <a-nav-link to="reports" title="Reports"/>
                <h3>Content</h3>
                <a-nav-link to="games" title="Games"/>
                <a-nav-link to="mods" title="Mods"/>
                <a-nav-link to="tags" title="Tags"/>
                <a-nav-link to="docs" title="Documents"/>
                <a-nav-link to="users" title="Users"/>
                <a-nav-link to="forum-categories" title="Forum Categories"/>
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