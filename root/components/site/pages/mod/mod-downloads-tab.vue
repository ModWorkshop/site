<template>
    <m-flex column gap="6">
        <m-list v-if="loadingFiles || files?.meta.total" v-model:page="filesPage" :limit="10" :title="$t('files')" :items="files" :loading="loadingFiles">
            <template #item="{ item }">
                <mod-download :file="item" :mod="mod" type="file"/>
            </template>
        </m-list>

        <m-list v-if="loadingLinks || links?.meta.total" v-model:page="linksPage" :limit="10" :title="$t('links')" :items="links" :loading="loadingLinks">
            <template #item="{ item }">
                <mod-download :file="item" :mod="mod" type="link"/>
            </template>
        </m-list>
    </m-flex>
</template>

<script setup lang="ts">
import type { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const filesPage = ref(1);
const linksPage = ref(1);

const { data: files, loading: loadingFiles } = await useWatchedFetchMany(`mods/${props.mod.id}/files`, { page: filesPage, limit: 10 }, { lazy: true });
const { data: links, loading: loadingLinks } = await useWatchedFetchMany(`mods/${props.mod.id}/links`, { page: linksPage, limit: 10 }, { lazy: true });
</script>