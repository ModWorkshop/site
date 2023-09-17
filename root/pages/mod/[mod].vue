<template>
    <mod-page v-if="mod" :mod="mod">
        <flex column gap="3">
            <NuxtPage v-model:mod="mod"/>
        </flex>
    </mod-page>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const { data: mod }: { data: Ref<Mod> } = await useResource<Mod>('mod', 'mods', {
    suspended: t('error_suspended'),
    rejected: t('error_rejected'),
    unapproved: t('error_unapproved'),
});
</script>