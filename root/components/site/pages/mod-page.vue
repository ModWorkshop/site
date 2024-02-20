<template>
    <Title v-if="mod.game">{{mod.name}} - {{ mod.game.name }} Mods</Title>
    <Title v-else>{{ mod.name }}</Title>
    <page-block v-if="usePageBlock" :game="mod.game" :breadcrumb="breadcrumb" :define-meta="false">
        <mod-alerts v-if="mod.id" :mod="mod"/>
        <mod-top-buttons v-if="mod.id" :mod="mod"/>
        <slot/>
    </page-block>
    <m-flex v-else gap="3" column>
        <mod-alerts v-if="mod.id" :mod="mod"/>
        <mod-top-buttons v-if="mod.id" :mod="mod"/>
        <slot/>
    </m-flex>
</template>

<script setup lang="ts">
import type { Breadcrumb, Mod } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';

const { mod, usePageBlock = true } = defineProps<{
    mod: Mod,
    usePageBlock?: boolean
}>();

const { t } = useI18n();
const { setGame } = useStore();
const route = useRoute();

const { public: config } = useRuntimeConfig();

const thumbnail = computed(() => {
    const thumb = mod.thumbnail;
    if (thumb) {
        return `${config.storageUrl}/mods/images/${thumb.has_thumb ? 'thumbnail_' : ''}${thumb.file}`;
    } else {
        return  `${config.siteUrl}/assets/no-preview-dark.png`;
    }
});

useServerSeoMeta({
    ogSiteName: `ModWorkshop - ${mod.game?.name} - Mod`,
    author: mod.user?.name,
	ogTitle: `${mod.name} by ${mod.user?.name}`,
	description: mod.short_desc,
	ogDescription: mod.short_desc,
	ogImage: thumbnail.value,
	twitterCard: 'summary',
});


if (mod.game) {
    setGame(mod.game);
}

if (mod.id && process.client) {
    postRequest(`mods/${mod.id}/register-view`, null, {
        onResponse(response: any) {
            if (response.status == 201) {
                mod.views++;
            }
        }
    });
}


const breadcrumb = computed(() => {
    const breadcrumb: Breadcrumb[] = [
        { name: t('games'), to: 'games' },
    ];

    if (mod.breadcrumb) {
        breadcrumb.push(...mod.breadcrumb);
    }

    if (route.name == 'mod-mod-edit') {
        breadcrumb.push({ name: t('edit') });
    }

    if (mod.id) {
        return breadcrumb;
    } else {
        return [];
    }
});
</script>