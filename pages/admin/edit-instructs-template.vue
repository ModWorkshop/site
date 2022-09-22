<template>
    <simple-resource-form v-model="template" :url="url" :ignore-changes="ignoreChanges">
        <a-input v-model="template.name" :label="$t('name')"/>
        <md-editor v-model="template.instructions" :label="$t('instructions')"/>
        <a-input v-model="template.localized" type="checkbox" :label="$t('localized')"/>
        <edit-dependencies v-if="template.id" :dependable="template" url="instructs-templates"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { InstructsTemplate } from '~~/types/models';

const route = useRoute();
const url = computed(() => `games/${route.params.gameId}/instructs-templates`);
const { data: template } = await useEditResource<InstructsTemplate>('template', url.value, {
    id: 0,
    name: '',
    instructions: '',
    localized: false,
    game_id: 0,
});

const ignoreChanges = useEventRaiser();

watch(template.value.dependencies, () => ignoreChanges.execute());
</script>