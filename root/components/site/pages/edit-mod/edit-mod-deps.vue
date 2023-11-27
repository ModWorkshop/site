<template>
    <m-select
        v-model="mod.instructs_template_id"
        :label="$t('instructions_template')"
        :url="`games/${mod.game_id}/instructs-templates`"
        clearable
        null-clear
    />
    <md-editor v-model="mod.instructions" :label="$t('instructions')" rows="12"/>
    <edit-mod-edit-dependencies :dependable="mod" url="mods"/>
</template>

<script setup lang="ts">
import type { Mod } from '~~/types/models';
const mod = defineModel<Mod>({ required: true });
</script>