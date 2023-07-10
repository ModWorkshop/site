<template>
    <simple-resource-form v-model="doc" url="documents" :game="game" :redirect-to="redirectTo">
        <a-input v-model="doc.name" :label="$t('name')"/>
        <a-input v-model="doc.url_name" :label="$t('url_name')" :desc="$t('url_name_desc')"/>
        <a-input v-model="doc.is_unlisted" :label="$t('unlisted')" type="checkbox"/>
        <md-editor v-model="doc.desc" :label="$t('description')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Document, Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-docs', props.game);

const route = useRoute();

const gameId = typeof route.params.game == 'string' ? parseInt(route.params.game) : null;
const redirectTo = computed(() => getAdminUrl('documents', props.game));

const { data: doc } = await useEditResource<Document>('document', 'documents', {
    id: 0,
    last_user_id: 0,
    name: '',
    url_name: '',
    desc: '',
    game_id: props.game?.id,
}, { game_id: gameId });
</script>