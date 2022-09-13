<template>
    <page-block size="sm">
        <the-breadcrumb v-if="game" :items="breadcrumb"/>
        <h2>Documents</h2>
        <content-block>
            <a-list url="documents" query :item-link="item => `${url}/${item.url_name}`" :params="{ game_id: game?.id }"/>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Game } from '~~/types/models';

const { t } = useI18n();

const { data: game } = await useResource<Game>('game', 'games');

const url = computed(() => game.value ? `/g/${game.value.short_name}/docs` : '/docs');

const breadcrumb = computed(() => {
    return [
        { name: game.value.name, id: game.value.short_name, type: 'game' },
        { name: t('docs') },
    ];
});
</script>