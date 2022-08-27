<template>
    <simple-resource-form v-model="tag" url="tags" :redirect-to="redirectTo">
        <div>
            <a-tag :color="tag.color">{{tag.name}}</a-tag>
        </div>
        <a-input v-model="tag.name" :label="$t('name')"/>
        <a-input v-model="tag.color" :label="$t('color')" type="color"/>
        <a-select v-model="tag.type" :options="types" :label="$t('type')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Tag } from "~~/types/models";

const route = useRoute();

const types = [
    { name: 'All', id: '' },
    { name: 'Mods', id: 'forum' },
    { name: 'Forums', id: 'mod' },
];

const redirectTo = computed(() => route.params.gameId ? `/admin/games/${route.params.gameId}/tags` : `/admin/tags`);

const { data: tag } = await useEditResource<Tag>('tag', 'tags', {
    id: 0,
    name: '',
    color: '#fff',
    notice: '',
    type: '',
    notice_type: 1,
    game_id: typeof route.params.gameId == 'string' ? parseInt(route.params.gameId) : null,
    notice_localized: true
});
</script>