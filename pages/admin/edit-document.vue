<template>
    <simple-resource-form v-model="doc" url="documents" :redirect-to="redirectTo">
        <a-input v-model="doc.name" :label="$t('name')"/>
        <a-input v-model="doc.url_name" :label="$t('url_name')"/>
        <md-editor v-model="doc.desc" :label="$t('description')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Document } from '~~/types/models';

const route = useRoute();

const gameId = typeof route.params.gameId == 'string' ? parseInt(route.params.gameId) : null;

const redirectTo = computed(() => gameId ? `/admin/games/${gameId}/docs` : `/admin/docs`);

const { data: doc } = await useEditResource<Document>('document', 'documents', {
    id: 0,
    last_user_id: 0,
    name: '',
    url_name: '',
    desc: '',
    game_id: typeof route.params.gameId == 'string' ? gameId : null,
}, { game_id: gameId });
</script>