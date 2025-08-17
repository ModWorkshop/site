<template>
    <m-flex column>
        <h3 v-if="game?.id">
            {{$t('game_name_settings', { game: game.name })}}
        </h3>
        <m-nav v-if="game?.id" side :root="root">
            <h3>{{$t('general')}}</h3>
            <m-nav-link to="" :title="$t('home')"/>
            <m-nav-link v-if="hasPermission('manage-game', game)" to="settings" :title="$t('settings')"/>
            <m-nav-link v-if="hasPermission('manage-roles', game)" to="roles" :title="$t('roles')"/>
            <h3 class="mt-2">{{$t('moderation')}}</h3>
            <m-nav-link v-if="moderateUsers" to="cases" :title="$t('cases')"/>
            <m-nav-link v-if="manageMods" to="approvals" :title="$t('approvals')"/>
            <m-nav-link v-if="moderateUsers" to="bans" :title="$t('bans')"/>
            <m-nav-link v-if="manageMods" to="suspensions" :title="$t('suspensions')"/>
            <m-nav-link v-if="moderateUsers" to="reports" :title="$t('reports')"/>
            <m-nav-link v-if="canSeeAuditLog" to="audit-log" :title="$t('audit_log')"/>
            <h3 class="mt-2">{{$t('content')}}</h3>
            <m-nav-link v-if="manageMods" to="mods" :title="$t('mods')"/>
            <m-nav-link v-if="hasPermission('manage-tags', game)" to="tags" :title="$t('tags')"/>
            <m-nav-link v-if="hasPermission('manage-docs', game)" to="documents" :title="$t('docs')"/>
            <m-nav-link v-if="hasPermission('manage-roles', game)" to="users" :title="$t('users')"/>
            <m-nav-link v-if="hasPermission('manage-categories', game)" to="categories" :title="$t('categories')"/>
            <m-nav-link v-if="hasPermission('manage-forum-categories', game)" to="forum-categories" :title="$t('forum_categories')"/>
            <m-nav-link v-if="hasPermission('manage-instructions', game)" to="instructs-templates" :title="$t('instructions_templates')"/>
            <m-nav-link v-if="manageMods" to="mod-managers" :title="$t('mod_managers')"/>
            <template #content>
                <NuxtPage :game="game"/>
            </template>
        </m-nav>
        <NuxtPage v-else :game="game"/>
    </m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Game } from '~/types/models';

definePageMeta({ alias: '/game/:game/admin' });

const { game } = defineProps<{
    game: Game
}>();

const route = useRoute();
const { user, hasPermission } = useStore();
const { t } = useI18n();

if (!user || !adminGamePagePerms.some(perm => hasPermission(perm, game))) {
    throw createError({ statusCode: 403, statusMessage: t('error_403'), fatal: true });
}

const moderateUsers = computed(() => hasPermission('moderate-users', game));
const manageMods = computed(() => hasPermission('manage-mods', game));
const canSeeAuditLog = computed(() => hasPermission('can-see-audit-log'));

const root = computed(() => route.fullPath.match(/\/(g|game)\/([a-z-0-9_]+)\/admin/)?.[0] ?? `/g/${game.id}/admin`);
</script>