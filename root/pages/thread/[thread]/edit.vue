<template>
    <edit-thread-page :game="thread.game" :thread="thread"/>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game, Thread } from '~~/types/models';

const store = useStore();

const { data: game } = await useResource<Game>('game', 'games');

const forumId = game.value ? game.value.forum_id : 1;

const { data: thread } = await useEditResource<Thread>('thread', 'threads', {
    id: 0,
    views: 0,
    locked: false,
    locked_by_mod: false,
    announce: false,
    name: '',
    content: '',
    tag_ids: [],
    user_id: store.user!.id,
    forum_id: forumId,
});
</script>