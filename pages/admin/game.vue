<template>
    <page-block :size="game.id ? 'md' : 'sm'" :game="game">
        <content-block class="p-6">
            <h3 v-if="game.id">
                {{$t('game_name_settings', { game: game.name })}}
            </h3>
            <a-nav side :root="`/admin/games/${id}`">
                <template v-if="game.id">
                    <h3>{{$t('general')}}</h3>
                    <a-nav-link to="" :title="$t('home')"/>
                    <a-nav-link v-if="hasPermission('manage-game', game)" to="settings" :title="$t('settings')"/>
                    <a-nav-link v-if="hasPermission('manage-roles', game)" to="roles" :title="$t('roles')"/>
                    <h3 class="mt-2">{{$t('moderation')}}</h3>
                    <a-nav-link v-if="moderateUsers" to="cases" :title="$t('cases')"/>
                    <a-nav-link v-if="moderateUsers" to="approvals" :title="$t('approvals')"/>
                    <a-nav-link v-if="moderateUsers" to="bans" :title="$t('bans')"/>
                    <a-nav-link v-if="manageMods" to="suspensions" :title="$t('suspensions')"/>
                    <a-nav-link v-if="moderateUsers" to="reports" :title="$t('reports')"/>
                    <h3 class="mt-2">{{$t('content')}}</h3>
                    <a-nav-link v-if="manageMods" to="mods" :title="$t('mods')"/>
                    <a-nav-link v-if="hasPermission('manage-tags', game)" to="tags" :title="$t('tags')"/>
                    <a-nav-link v-if="hasPermission('manage-docs', game)" to="docs" :title="$t('docs')"/>
                    <a-nav-link v-if="hasPermission('manage-categories', game)" to="categories" :title="$t('categories')"/>
                    <a-nav-link v-if="hasPermission('manage-forum-categories', game)" to="forum-categories" :title="$t('forum_categories')"/>
                    <a-nav-link v-if="hasPermission('manage-instructions', game)" to="instructions-templates" :title="$t('instructions_templates')"/>
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
import { adminGamePagePerms } from '~~/utils/helpers';

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
}, { include: 'roles' });

if (!user || !adminGamePagePerms.some(perm => hasPermission(perm, game.value))) {
    throw createError({ statusCode: 403, statusMessage: t('error_403'), fatal: true });
}

const moderateUsers = computed(() => hasPermission('moderate-users', game.value));
const manageMods = computed(() => hasPermission('manage-mods', game.value));

</script>