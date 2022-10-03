<template>
    <page-block :size="game.id ? 'md' : 'sm'">
        <content-block class="p-6">
            <h3 v-if="game.id">
                {{game.name}} Game Settings
            </h3>
            <a-nav side :root="`/admin/games/${id}`">
                <template v-if="game.id">
                    <h3>General</h3>
                    <a-nav-link to="" title="Home"/>
                    <a-nav-link v-if="hasPermission('manage-game', game)" to="settings" title="Settings"/>
                    <a-nav-link v-if="hasPermission('manage-roles', game)" to="roles" title="Roles"/>
                    <h3>Moderation</h3>
                    <a-nav-link v-if="moderateUsers" to="cases" title="Cases"/>
                    <a-nav-link v-if="moderateUsers" to="approvals" title="Approvals"/>
                    <a-nav-link v-if="moderateUsers" to="bans" title="Bans"/>
                    <a-nav-link v-if="manageMods" to="suspensions" title="Suspensions"/>
                    <a-nav-link v-if="moderateUsers" to="reports" title="Reports"/>
                    <h3>Content</h3>
                    <a-nav-link v-if="manageMods" to="mods" title="Mods"/>
                    <a-nav-link v-if="hasPermission('manage-tags', game)" to="tags" title="Tags"/>
                    <a-nav-link v-if="hasPermission('manage-docs', game)" to="docs" title="Documents"/>
                    <a-nav-link v-if="hasPermission('manage-categories', game)" to="categories" title="Categories"/>
                    <a-nav-link v-if="hasPermission('manage-forum-categories', game)" to="forum-categories" title="Forum Categories"/>
                    <a-nav-link v-if="hasPermission('manage-instructions', game)" to="instructions-templates" title="Instructions Templates"/>
                </template>
                <template #content>
                    <NuxtPage :game="game"/>
                </template>
            </a-nav>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';

const route = useRoute();
const id = computed(() => route.params.gameId);
const { user, hasPermission } = useStore();
const { t } = useI18n();

const { data: game } = await useEditResource<Game>('game', 'games', {
    id: 0,
    forum_id: 0,
    name: '',
    short_name: "",
    thumbnail: "",
    banner: "",
    buttons: "",
    webhook_url: "",
    last_date: "",
    created_at: "",
    updated_at: ""
});

if (!user || !adminGamePagePerms.some(perm => hasPermission(perm, game.value))) {
    throw createError({ statusCode: 403, statusMessage: t('error_403'), fatal: true });
}

const moderateUsers = computed(() => hasPermission('moderate-users', game.value));
const manageMods = computed(() => hasPermission('manage-mods', game.value));

</script>