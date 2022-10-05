<template>
    <a-select v-model="mod.instructs_template_id" :url="`games/${mod.game_id}/instructs-templates`"/>
    <md-editor v-model="mod.instructions" :label="$t('instructions')" rows="12"/>
    <edit-dependencies :dependable="mod" url="mods"/>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod,
}>();

const ignoreChanges: () => void = inject('ignoreChanges');

watch(props.mod.dependencies, ignoreChanges);
</script>