<template>
    <simple-resource-form v-model="tag" url="tags" :redirect-to="redirectTo">
        <div>
            <a-tag :color="tag.color">{{tag.name}}</a-tag>
        </div>
        <a-input v-model="tag.name" :label="$t('name')"/>
        <a-input v-model="tag.color" :label="$t('color')" type="color"/>
        <a-select v-model="tag.type" :options="types" :label="$t('type')"/>
        <md-editor v-model="tag.notice" :label="$t('notice')"/>
        <a-select v-model="tag.notice_type" :options="noticeTypes" :label="$t('notice_type')"/>
        <a-input v-model="tag.notice_localized" :label="$t('notice_localized')" type="checkbox"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Game, Tag } from "~~/types/models";

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-tags', props.game);

const route = useRoute();

const types = [
    { name: 'All', id: '' },
    { name: 'Mods', id: 'forum' },
    { name: 'Forums', id: 'mod' },
];

const noticeTypes = [
    { name: 'Info', id: 'info' },
    { name: 'Warning', id: 'warning' },
    { name: 'Danger', id: 'danger' },
];

const redirectTo = computed(() => route.params.gameId ? `/admin/games/${route.params.gameId}/tags` : `/admin/tags`);

const { data: tag } = await useEditResource<Tag>('tag', 'tags', {
    id: 0,
    name: '',
    color: '#fff',
    notice: '',
    type: '',
    notice_type: 'info',
    game_id: typeof route.params.gameId == 'string' ? parseInt(route.params.gameId) : null,
    notice_localized: true
});
</script>