<template>
    <page-block v-if="mod" :game="mod.game" :breadcrumb="breadcrumb">
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

if (props.mod.game) {
    setGame(props.mod.game);
}

usePost(`mods/${props.mod.id}/register-view`, null, {
    async onResponse({ response }) {
        if (response.status == 201) {
            props.mod.views++;
        }
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