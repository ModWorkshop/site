<template>
    <page-block size="md">
        <content-block class="p-6">
            <a-nav side root="/admin">
                <h3>{{$t('general')}}</h3>
                <a-nav-link to="" :title="$t('home')"/>
                <a-nav-link v-if="hasPermission('admin')" to="settings" :title="$t('settings')"/>
                <a-nav-link v-if="hasPermission('manage-roles')" to="roles" :title="$t('roles')"/>
                <a-nav-link v-if="hasPermission('manage-users')" to="supporters" :title="$t('supporters')"/>
                <h3 class="mt-2">{{$t('moderation')}}</h3>
                <a-nav-link v-if="moderateUsers" to="cases" :title="$t('cases')"/>
                <a-nav-link v-if="moderateUsers" to="approvals" :title="$t('approvals')"/>
                <a-nav-link v-if="moderateUsers" to="bans" :title="$t('bans')"/>
                <a-nav-link v-if="manageMods" to="suspensions" :title="$t('suspensions')"/>
                <a-nav-link v-if="moderateUsers" to="reports" :title="$t('reports')"/>
                <h3 class="mt-2">{{$t('content')}}</h3>
                <a-nav-link to="games" :title="$t('games')"/>
                <a-nav-link v-if="manageMods" to="mods" :title="$t('mods')"/>
                <a-nav-link v-if="hasPermission('manage-tags')" to="tags" :title="$t('tags')"/>
                <a-nav-link v-if="hasPermission('manage-docs')" to="documents" :title="$t('documents')"/>
                <a-nav-link v-if="hasPermission('manage-users')" to="users" :title="$t('users')"/>
                <a-nav-link v-if="hasPermission('manage-forum-categories')" to="forum-categories" :title="$t('forum_categories')"/>
                <a-nav-link v-if="manageMods" to="mod-managers" :title="$t('mod_managers')"/>
                <template #content>
                    <NuxtPage/>
                </template>
            </a-nav>
        </content-block>
    </page-block>
</template>

<script setup>
import { useStore } from '~~/store';
import { adminPagePerms } from '~~/utils/helpers';

const { user, hasPermission } = useStore();
    
if (!user || !adminPagePerms.some(perm => hasPermission(perm))) {
    throw createError({ statusCode: 403, statusMessage: t('error_403'), fatal: true });
}

const moderateUsers = computed(() => hasPermission('moderate-users'));
const manageMods = computed(() => hasPermission('manage-mods'));
</script>