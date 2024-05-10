<template>
    <Title v-if="mod.game">{{mod.name}} - {{ mod.game.name }} Mods</Title>
    <Title v-else>{{ mod.name }}</Title>
    <page-block v-if="usePageBlock" show-background :game="mod.game" :background="modBackground" :background-opacity="modBackgroundOpacity" :breadcrumb="breadcrumb" :define-meta="false">
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

const { usePageBlock = true } = defineProps<{
    usePageBlock?: boolean
}>();

const mod = defineModel<Mod>('mod', { required: true });

const { t } = useI18n();
const { setGame } = useStore();
const route = useRoute();

const { public: config } = useRuntimeConfig();

const thumbnail = computed(() => {
    const thumb = mod.value.thumbnail;
    if (thumb) {
        return `${config.storageUrl}/mods/images/${thumb.has_thumb ? 'thumbnail_' : ''}${thumb.file}`;
    } else {
        return `${config.siteUrl}/assets/no-preview.webp`;
    }
});

useServerSeoMeta({
    ogSiteName: `ModWorkshop - ${mod.value.game?.name} - Mod`,
    author: mod.value.user?.name,
	ogTitle: `${mod.value.name} by ${mod.value.user?.name}`,
	description: mod.value.short_desc,
	ogDescription: mod.value.short_desc,
	ogImage: thumbnail.value,
	twitterCard: 'summary',
});


if (mod.value.game) {
    setGame(mod.value.game);
}

if (mod.value.id && process.client) {
    postRequest(`mods/${mod.value.id}/register-view`, null, {
        onResponse(response: any) {
            if (response.status == 201) {
                mod.value.views++;
            }
        }
    });
}


const breadcrumb = computed(() => {
    const breadcrumb: Breadcrumb[] = [
        { name: t('games'), to: 'games' },
    ];

    if (mod.value.breadcrumb) {
        breadcrumb.push(...mod.value.breadcrumb);
    }

    if (route.name == 'mod-mod-edit') {
        breadcrumb.push({ name: t('edit') });
    }

    if (mod.value.id) {
        return breadcrumb;
    } else {
        return [];
    }
});

const modBackground = computed(() => {
    if (mod.value.user?.has_supporter_perks && mod.value.background) {
        return useSrc('mods/images', mod.value.background.file);
    }
});

const modBackgroundOpacity = computed(() => {
    if (mod.value.user?.has_supporter_perks && mod.value.background) {
        return mod.value.background_opacity;
    }
});
</script>