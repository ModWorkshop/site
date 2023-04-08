<template>
    <page-block size="sm" :game="g" :breadcrumb="breadcrumb">
        <content-block class="p-8">
            <simple-resource-form 
                v-model="thread"
                url="threads"
                redirect-to="/thread"
                :create-url="`forums/${forumId}/threads`"
                :delete-redirect-to="deleteRedirectTo"
            >
                <a-input v-model="thread.name" :label="$t('title')"/>
                <md-editor v-model="thread.content" :label="$t('content')"/>
                <a-select v-model="thread.category_id" :label="$t('category')" :options="allowedCategories" clearable/>
                <a-select v-model="thread.tag_ids" :options="tags?.data" multiple :label="$t('tags')"/>
                <template v-if="canManage">
                    <a-input v-model="thread.announce" type="checkbox" :label="$t('announce')"/>
                    <a-duration v-if="thread.announce" v-model="thread.announce_until" :label="$t('announcement_duration')"/>
                </template>
            </simple-resource-form>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, ForumCategory, Game, Tag, Thread } from '~~/types/models';

const store = useStore();
const { isBanned, ban, gameBan, user } = storeToRefs(store);

const { t } = useI18n();

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
    user_id: user.value!.id,
    forum_id: forumId,
});

const g = game.value ?? thread.value?.forum?.game;
store.setGame(g);

if (!useCanEditThread(thread.value, g)) {
    useNoPermsPage();
}

const canManage = computed(() => store.hasPermission('manage-discussions', game.value));

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

const allowedCategories = computed(() => {
    const canAppeal = ban.value?.can_appeal ?? true;
    const canAppealGame = gameBan.value?.can_appeal ?? true;

    return categories.value?.data.filter(cat => cat.can_post && (!isBanned.value || (canAppeal && canAppealGame))) ?? [];
});

if (!thread.value.id && isBanned.value && !allowedCategories.value.length) {
    useNoPermsPage();
}

const threadGame = computed(() => game.value ?? (thread.value.forum ? thread.value.forum.game : null));

const deleteRedirectTo = computed(() => threadGame.value ? `/g/${threadGame.value.short_name}/forum` : `/forum`);

const breadcrumb = computed(() => {
    let crumbs: Breadcrumb[] = [
        { name: t('forum'), attachToPrev: 'forum' }
    ];

    if (threadGame.value) {
        crumbs.unshift({ name: threadGame.value.name, id: threadGame.value.short_name, type: 'game' });
    }

    if (thread.value.id && thread.value.category) {
        crumbs.push({ name: thread.value.category.name, id: thread.value.category.name, type: 'forum_category' });
    }
    
    crumbs.push({ name: thread.value.id ? thread.value.name : t('post'), id: thread.value.id, type: 'thread' });
    if (thread.value.id) {
        crumbs.push({ name: t('edit') });
    }

    return crumbs;
});
</script>