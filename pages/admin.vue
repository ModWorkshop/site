<template>
    <page-block size="md">
        <content-block class="p-6">
            <a-nav side root="/admin">
                <h3>General</h3>
                <a-nav-link to="" title="Home"/>
                <a-nav-link v-if="hasPermission('admin')" to="settings" title="Settings"/>
                <a-nav-link v-if="hasPermission('manage-roles')" to="roles" title="Roles"/>
                <h3>Moderation</h3>
                <a-nav-link v-if="moderateUsers" to="cases" title="Cases"/>
                <a-nav-link v-if="moderateUsers" to="approvals" title="Approvals"/>
                <a-nav-link v-if="moderateUsers" to="bans" title="Bans"/>
                <a-nav-link v-if="manageMods" to="suspensions" title="Suspensions"/>
                <a-nav-link v-if="moderateUsers" to="reports" title="Reports"/>
                <h3>Content</h3>
                <a-nav-link to="games" title="Games"/>
                <a-nav-link v-if="manageMods" to="mods" title="Mods"/>
                <a-nav-link v-if="hasPermission('manage-tags')" to="tags" title="Tags"/>
                <a-nav-link v-if="hasPermission('manage-docs')" to="docs" title="Documents"/>
                <a-nav-link v-if="hasPermission('manage-users')" to="users" title="Users"/>
                <a-nav-link v-if="hasPermission('manage-forum-categories')" to="forum-categories" title="Forum Categories"/>
                <template #content>
                    <NuxtPage/>
                </template>
            </a-nav>
        </content-block>
    </page-block>
</template>

<script setup>
import { useStore } from '~~/store';

const { user, hasPermission } = useStore();
    
if (!user || !adminPagePerms.some(perm => hasPermission(perm))) {
    throw createError({ statusCode: 403, statusMessage: t('error_403'), fatal: true });
}

const moderateUsers = computed(() => hasPermission('moderate-users'));
const manageMods = computed(() => hasPermission('manage-mods'));
</script>