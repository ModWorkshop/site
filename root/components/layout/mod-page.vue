<template>
    <page-block v-if="mod" :game="mod.game" :breadcrumb="breadcrumb" :define-meta="false">
        <Title>{{mod.name}}</Title>
        <mod-alerts :mod="mod"/>
        <mod-buttons :mod="mod"/>
        <slot/>
    </page-block>
</template>

<script setup lang="ts">
import { Breadcrumb, Mod } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';

const props = defineProps<{
    mod: Mod
}>();

const { t } = useI18n();
const { setGame } = useStore();

const { public: config } = useRuntimeConfig();

const thumbnail = computed(() => {
    const thumb = props.mod.thumbnail;
    if (thumb) {
        return `${config.storageUrl}/mods/images/${thumb.has_thumb ? 'thumbnail_' : ''}${thumb.file}`;
    } else {
        return  `${config.siteUrl}/assets/no-preview-dark.png`;
    }
});

useServerSeoMeta({
    ogSiteName: `ModWorkshop - ${props.mod.game?.name} - Mod`,
    author: props.mod.user?.name,
	ogTitle: `${props.mod.name} by ${props.mod.user?.name}`,
	description: props.mod.short_desc,
	ogDescription: props.mod.short_desc,
	ogImage: thumbnail.value,
	twitterCard: 'summary',
});


if (props.mod.game) {
    setGame(props.mod.game);
}

postRequest(`mods/${props.mod.id}/register-view`).then((response: any) => {
    if (response.status == 201) {
        props.mod.views++;
    }
});

const breadcrumb = computed(() => {
    const breadcrumb: Breadcrumb[] = [
        { name: t('games'), to: 'games' },
    ];
    if (props.mod.breadcrumb) {
        breadcrumb.push(...props.mod.breadcrumb);
    }

    return breadcrumb;
});
</script>