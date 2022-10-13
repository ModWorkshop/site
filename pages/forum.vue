<template>
    <page-block>
        <the-breadcrumb v-if="game" :items="breadcrumb"/>
        <Title>{{game ? `${game.name} Forum` : $t('forum')}}</Title>
        <flex>
            <a-button :to="newThreadLink">{{$t('new_thread')}}</a-button>
        </flex>
        <thread-list :game-id="game?.id" :forum-id="forumId"/>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';

const store = useStore();
const { t } = useI18n();

const { data: game } = await useResource<Game>('game', 'games');

store.setGame(game.value);

const breadcrumb = computed(() => {
    return [
        { name: game.value.name, id: game.value.short_name, type: 'game' },
        { name: t('forum') },
    ];
});

const forumId = game.value ? game.value.forum_id : 1;

const newThreadLink = computed(() => game.value ? `/g/${game.value.short_name}/forum/post` : '/forum/post');
</script>