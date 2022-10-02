<template>
    <page-block size="md">
        <content-block class="p-6">
            {{game.name}} Game Settings
            <a-nav side :root="`/admin/games/${id}`">
                <a-nav-link to="" title="Main"/>
                <a-nav-link to="tags" title="Tags"/>
                <a-nav-link to="docs" title="Documents"/>
                <a-nav-link to="categories" title="Categories"/>
                <a-nav-link to="roles" title="Roles"/>
                <a-nav-link to="forum-categories" title="Forum Categories"/>
                <a-nav-link to="instructions-templates" title="Instructions Templates"/>
                <a-nav-link to="mods" title="Mods"/>
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
</script>