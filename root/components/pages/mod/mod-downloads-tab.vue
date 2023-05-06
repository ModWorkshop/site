<template>
    <flex column gap="6">
        <a-items v-if="compFiles && compFiles?.meta.total > 0" v-model:page="filesPage" :title="$t('files')" :items="compFiles" :loading="loadingFiles">
            <template #item="{ item }">
                <mod-download :file="item" :mod="mod" type="file"/>
            </template>
        </a-items>

        <a-items v-if="compLinks && compLinks?.meta.total > 0" v-model:page="linksPage" :title="$t('links')" :items="compLinks" :loading="loadingLinks">
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

const { data: files, loading: loadingFiles } = await useWatchedFetchMany(`mods/${props.mod.id}/files`, { page: filesPage, limit: 5 }, {
    immediate: false
});
const { data: links, loading: loadingLinks } = await useWatchedFetchMany(`mods/${props.mod.id}/links`, { page: linksPage, limit: 5 }, {
    immediate: false
});

const compFiles = computed(() => files.value ?? props.mod.files);
const compLinks = computed(() => links.value ?? props.mod.links);

</script>