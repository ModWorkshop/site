<template>
    <page-block size="sm">
        <the-breadcrumb :items="breadcrumb"/>
        <content-block class="p-8">
            <simple-resource-form v-model="thread" url="threads" redirect-to="/thread" :delete-redirect-to="deleteRedirectTo">
                <a-input v-model="thread.name" :label="$t('title')"/>
                <md-editor v-model="thread.content" :label="$t('content')"/>
                <a-select v-model="thread.category_id" :label="$t('category')" :options="categories.data"/>
                <a-select v-model="thread.tag_ids" placeholder="Select tags" :options="tags.data" multiple label="Tags"/>
            </simple-resource-form>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, ForumCategory, Game, Tag, Thread } from '~~/types/models';

const { user } = useStore();
const { t } = useI18n();

const { data: game } = await useResource<Game>('game', 'games');
const forumId = game.value ? game.value.forum_id : 1;

const { data: thread } = await useEditResource<Thread>('thread', 'threads', {
    id: 0,
    views: 0,
    archived: false,
    archived_by_mod: false,
    name: '',
    content: '',
    tag_ids: [],
    category_id: null,
    user_id: user.id,
    forum_id: forumId,
});

const { data: tags } = await useFetchMany<Tag>('tags', { 
    params: { 
        game_id: game.value?.id,
        type: 'forum',
        global: 1
    }
});

const { data: categories } = await useFetchMany<ForumCategory>('forum-categories', {
    params: {
        forum_id: thread.value.forum_id
    }
});

const threadGame = computed(() => game.value ?? (thread.value.forum ? thread.value.forum.game : null));

const deleteRedirectTo = computed(() => threadGame.value ? `/g/${threadGame.value.short_name}/forum` : `/forum`);

const breadcrumb = computed(() => {
    let crumbs: Breadcrumb[] = [
        { name: t('forum'), attachToPrev: 'forum' }
    ];

    if (threadGame.value) {
        crumbs.push({ name: threadGame.value.name, id: threadGame.value.short_name, type: 'game' });
    }

    crumbs.push({ name: thread.value.id ? thread.value.name : t('post'), id: thread.value.id, type: 'thread' });
    if (thread.value.id) {
        crumbs.push({ name: t('edit') });
    }

    return crumbs;
});
</script>