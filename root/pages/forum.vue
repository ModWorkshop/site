<template>
    <page-block :game="game" :breadcrumb="game ? breadcrumb : undefined" game-banner>
        <Title>{{game ? $t('name_forum', { name: game.name }) : $t('forum')}}</Title>
        <flex>
            <a-button :to="newThreadLink" :disabled="cannotPost">{{$t('new_thread')}}</a-button>
        </flex>
        <thread-list :game-id="game?.id" :forum-id="forumId" @select-category="cat => category = cat"/>
    </page-block>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { ForumCategory, Game } from '~~/types/models';

const store = useStore();
const { isBanned, ban, gameBan } = storeToRefs(useStore());
const { t } = useI18n();

const category = ref<ForumCategory>();
const { data: game } = await useResource<Game>('game', 'games');

store.setGame(game.value);
const cannotPost = computed(() => {
    const canAppeal = ban.value?.can_appeal ?? true;
    const canAppealGame = gameBan.value?.can_appeal ?? true;

    return isBanned.value && (!category.value?.banned_can_post || !canAppeal || !canAppealGame);
});

const breadcrumb = computed(() => {
    return [
        { name: t('games'), to: 'games' },
        { name: game.value.name, id: game.value.short_name, type: 'game' },
        { name: t('forum') },
    ];
});

const forumId = game.value ? game.value.forum_id : 1;

const newThreadLink = computed(() => game.value ? `/g/${game.value.short_name}/forum/post` : '/forum/post');
</script>