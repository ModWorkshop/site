<template>
    <simple-resource-form 
        v-model="template"
        url="instructs-templates"
        :game="game"
        :redirect-to="redirectTo"
        :exclude-from-compare="['dependencies']"
    >
        <a-input v-model="template.name" :label="$t('name')"/>
        <md-editor v-model="template.instructions" :label="$t('instructions')"/>
        <a-input v-model="template.localized" type="checkbox" :label="$t('localized')"/>
        <edit-mod-edit-dependencies v-if="template.id" :dependable="template" url="instructs-templates"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import type { Game, InstructsTemplate } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-instructions', props.game);

const redirectTo = getAdminUrl('instructs-templates', props.game);

const { data: template } = await useEditResource<InstructsTemplate>('template', 'instructs-templates', {
    id: 0,
    name: '',
    instructions: '',
    localized: false,
    game_id: props.game.id,
});
</script>