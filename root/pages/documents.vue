<template>
    <page-block :game="game" :breadcrumb="game ? breadcrumb : undefined" size="sm">
        <h2>{{$t('documents')}}</h2>
        <content-block>
            <a-list :url="apiUrl" query :item-link="item => `${url}/${item.url_name}`" :params="{ game_id: game?.id }"/>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';

const { t } = useI18n();
const store = useStore();

const { data: game } = await useResource<Game>('game', 'games');

store.setGame(game.value);

const url = computed(() => game.value ? `/g/${game.value.short_name}/document` : '/document');
const apiUrl = computed(() => getGameResourceUrl('documents', game.value));

const breadcrumb = computed(() => {
    return [
        { name: game.value.name, id: game.value.short_name, type: 'game' },
        { name: t('docs') },
    ];
});
</script>