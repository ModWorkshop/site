<template>
    <page-block size="md">
        <m-flex class="p-6">
            <m-nav side root="/admin">
                <h3>{{$t('general')}}</h3>
                <m-nav-link to="" :title="$t('home')"/>
                <m-nav-link v-if="hasPermission('admin')" to="settings" :title="$t('settings')"/>
                <m-nav-link v-if="hasPermission('manage-roles')" to="roles" :title="$t('roles')"/>
                <m-nav-link v-if="hasPermission('manage-users')" to="supporters" :title="$t('supporters')"/>
                <h3 class="mt-2">{{$t('moderation')}}</h3>
                <m-nav-link v-if="moderateUsers" to="cases" :title="$t('cases')"/>
                <m-nav-link v-if="moderateUsers" to="approvals" :title="$t('approvals')"/>
                <m-nav-link v-if="moderateUsers" to="bans" :title="$t('bans')"/>
                <m-nav-link v-if="manageMods" to="suspensions" :title="$t('suspensions')"/>
                <m-nav-link v-if="moderateUsers" to="reports" :title="$t('reports')"/>
                <h3 class="mt-2">{{$t('content')}}</h3>
                <m-nav-link to="games" :title="$t('games')"/>
                <m-nav-link v-if="manageMods" to="mods" :title="$t('mods')"/>
                <m-nav-link v-if="hasPermission('manage-tags')" to="tags" :title="$t('tags')"/>
                <m-nav-link v-if="hasPermission('manage-docs')" to="documents" :title="$t('documents')"/>
                <m-nav-link v-if="hasPermission('manage-users')" to="users" :title="$t('users')"/>
                <m-nav-link v-if="hasPermission('manage-forum-categories')" to="forum-categories" :title="$t('forum_categories')"/>
                <m-nav-link v-if="manageMods" to="mod-managers" :title="$t('mod_managers')"/>
                <template #content>
                    <NuxtPage/>
                </template>
            </m-nav>
        </m-flex>
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