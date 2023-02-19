<template>
    <page-block :game="game" :breadcrumb="breadcrumb" game-banner>
        <Title>{{game.name}}</Title>
        <mod-list 
            v-if="store.user?.extra?.game_show_mods ?? true"
            :title="$t('mods')"
            :title-link="`/g/${game.short_name}/mods`"
            :game="game"
            :url="`games/${game.id}/mods`"
        />
        <thread-list 
            v-if="store.user?.extra?.game_show_threads ?? true"
            :title="$t('threads')"
            :title-link="`/g/${game.short_name}/forum`"
            :game-id="game?.id"
            :forum-id="game?.forum_id"
            :limit="10"
            :query="false"
            :filters="false"
        />
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';
const store = useStore();
const { t } = useI18n();

definePageMeta({ alias: '/g/:game' });

const { data: game } = await useResource<Game>('game', 'games');

const breadcrumb = computed(() => {
    if (game.value) {
        return [ { name: t('games'), to: 'games' }, { name: game.value.name } ];
    }
});

watch(() => game.value, () =>  store.currentGame = game.value, { immediate: true });
</script>