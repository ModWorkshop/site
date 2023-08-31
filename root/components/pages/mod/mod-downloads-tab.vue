<template>
    <flex column gap="6">
        <a-items v-if="loadingFiles || files?.meta.total" v-model:page="filesPage" :title="$t('files')" :items="files" :loading="loadingFiles">
            <template #item="{ item }">
                <mod-download :file="item" :mod="mod" type="file"/>
            </template>
        </a-items>

        <a-items v-if="loadingLinks || links?.meta.total" v-model:page="linksPage" :title="$t('links')" :items="links" :loading="loadingLinks">
            <template #item="{ item }">
                <mod-download :file="item" :mod="mod" type="link"/>
            </template>
        </a-items>
    </flex>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const filesPage = ref(1);
const linksPage = ref(1);

const { data: files, loading: loadingFiles } = await useWatchedFetchMany(`mods/${props.mod.id}/files`, { page: filesPage, limit: 5 });
const { data: links, loading: loadingLinks } = await useWatchedFetchMany(`mods/${props.mod.id}/links`, { page: linksPage, limit: 5 });
</script>