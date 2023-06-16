<template>
    <a-select v-model="mod.instructs_template_id" :label="$t('instructions_template')" :url="`games/${mod.game_id}/instructs-templates`" clearable/>
    <md-editor v-model="mod.instructions" :label="$t('instructions')" rows="12"/>
    <edit-mod-edit-dependencies :dependable="mod" url="mods"/>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';
defineProps<{
    mod: Mod,
}>();
</script>