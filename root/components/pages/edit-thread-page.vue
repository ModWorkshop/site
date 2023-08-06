<template>
    <content-block class="p-8 page-block-sm">
        <simple-resource-form 
            v-model="editThread"
            url="threads"
            redirect-to="/thread"
            :create-url="`forums/${forumId}/threads`"
            :delete-redirect-to="deleteRedirectTo"
        >
            <a-input v-model="editThread.name" :label="$t('title')"/>
            <md-editor v-model="editThread.content" minlength="2" maxlength="5000" :label="$t('content')"/>
            <a-select v-model="editThread.category_id" :label="$t('category')" :options="allowedCategories" clearable/>
            <a-select v-model="editThread.tag_ids" :options="tags?.data" multiple :label="$t('tags')"/>
            <template v-if="canManage">
                <a-input v-model="editThread.announce" type="checkbox" :label="$t('announce')"/>
                <a-duration v-if="editThread.announce" v-model="editThread.announce_until" :label="$t('announcement_duration')"/>
            </template>
        </simple-resource-form>
    </content-block>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, ForumCategory, Game, Tag, Thread } from '~~/types/models';

const store = useStore();
const { isBanned, ban, gameBan } = storeToRefs(store);

const { t } = useI18n();
const { user } = useStore();

const { game, thread } = defineProps<{
    game?: Game;
    thread?: Thread;
}>();

const forumId = game ? game.forum_id : 1;

const editThread = ref(thread ?? {
    id: 0,
    views: 0,
    locked: false,
    locked_by_mod: false,
    announce: false,
    name: '',
    content: '',
    tag_ids: [],
    user_id: user!.id,
    forum_id: forumId,
});

const g = game ?? thread?.forum?.game;
store.setGame(g);

if (!useCanEditThread(editThread, g)) {
    useNoPermsPage();
}

const canManage = computed(() => store.hasPermission('manage-discussions', game));

const { data: tags } = await useFetchMany<Tag>('tags', { 
    params: { 
        game_id: game?.id,
        type: 'forum',
        global: 1
    }
});

const { data: categories } = await useFetchMany<ForumCategory>('forum-categories', {
    params: {
        forum_id: editThread.forum_id
    }
});

const allowedCategories = computed(() => {
    const canAppeal = ban.value?.can_appeal ?? true;
    const canAppealGame = gameBan.value?.can_appeal ?? true;

    return categories.value?.data.filter(cat => cat.can_post && (!isBanned.value || (canAppeal && canAppealGame))) ?? [];
});

if (!editThread.id && isBanned.value && !allowedCategories.value.length) {
    useNoPermsPage();
}

const threadGame = computed(() => game ?? (editThread.forum ? editThread.forum.game : null));

const deleteRedirectTo = computed(() => threadGame.value ? `/g/${threadGame.value.short_name}/forum` : `/forum`);

const breadcrumb = computed(() => {
    let crumbs: Breadcrumb[] = [
        { name: t('forum'), attachToPrev: 'forum' }
    ];

    if (threadGame.value) {
        crumbs.unshift({ name: threadGame.value.name, id: threadGame.value.short_name, type: 'game' });
    }

    if (editThread.id && editThread.category) {
        crumbs.push({ name: editThread.category.name, id: editThread.category.id, type: 'forum_category' });
    }
    
    crumbs.push({ name: editThread.id ? editThread.name : t('post'), id: editThread.id, type: 'thread' });
    if (editThread.id) {
        crumbs.push({ name: t('edit') });
    }

    return crumbs;
});
</script>