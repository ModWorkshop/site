<template>
    <m-flex column gap="3">
        <Title>{{game ? $t('name_forum', { name: game.name }) : $t('forum')}}</Title>
        <m-flex>
            <m-button :to="newThreadLink" :disabled="cannotPost">{{$t('new_thread')}}</m-button>
        </m-flex>
        <thread-list :game-id="game?.id" :forum-id="forumId" @select-category="cat => category = cat"/>
    </m-flex>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useStore } from '~/store';
import type { ForumCategory, Game } from '~/types/models';

const store = useStore();
const { isBanned, ban, gameBan } = storeToRefs(useStore());

const { game } = defineProps<{
    game?: Game
}>();

const category = ref<ForumCategory>();

if (game) {
    store.setGame(game);
}

const cannotPost = computed(() => {
    const canAppeal = ban.value?.can_appeal ?? true;
    const canAppealGame = gameBan.value?.can_appeal ?? true;

    return isBanned.value && (!category.value?.banned_can_post || !canAppeal || !canAppealGame);
});

const forumId = game ? game.forum_id : 1;

const newThreadLink = computed(() => {
    return (game ? `/g/${game.short_name}/forum/post` : '/forum/post') + (category.value ? `?category=${category.value.id}` : '');
});
</script>