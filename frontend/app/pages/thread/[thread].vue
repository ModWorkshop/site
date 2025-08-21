<template>
    <page-block :game="threadGame" :breadcrumb="breadcrumb">
        <Title>{{thread.name}}</Title>
        <the-tag-notices v-if="thread.tags" :tags="thread.tags"/>
        <m-alert v-if="thread.locked" color="warning" :desc="lockedReason"/>
        <m-alert v-if="canCloseInCategory && thread.closed" color="info" :desc="$t('thread_closed')"/>
        <m-flex>
            <NuxtLink v-if="$route.name == 'thread-thread-edit'" :to="`/thread/${thread.id}`">
                <m-button><i-mdi-arrow-left/> {{$t('return_to_thread')}}</m-button>
            </NuxtLink>

            <m-button v-else-if="canEdit" :to="`/thread/${thread.id}/edit`"><i-mdi-cog/> {{$t('edit')}}</m-button>
            <m-button v-if="canEdit" :disabled="(thread.locked_by_mod && !canModerate)" @click="lockThread">
                <i-mdi-lock-open v-if="thread.locked"/>
                <i-mdi-lock v-else/>
                {{thread.locked ? $t('unlock') : $t('lock')}}
            </m-button>
            <m-button v-if="canCloseInCategory && canEdit" :disabled="(thread.closed_by_mod && !canModerate)" @click="closeThread">
                <i-mdi-undo v-if="thread.closed"/>
                <i-mdi-check-circle-outline v-else/>
                {{thread.closed ? $t('open') : $t('close')}}
            </m-button>

            <report-modal v-model:show-modal="showReportModal" resource-name="thread" :url="`/threads/${thread.id}/reports`"/>

            <m-dropdown>
                <m-button><i-mdi-dots-vertical/></m-button>
                <template #content>
                    <template v-if="canModerate">
                        <m-dropdown-item @click="pinThread"><i-mdi-pin/> {{thread.pinned_at ? $t('unpin') : $t('pin')}}</m-dropdown-item>
                        <m-dropdown-item @click="showMoveThread = true"><i-mdi-cursor-move/> {{$t('move')}}</m-dropdown-item>
                    </template>
                    <m-dropdown-item :to="!user ? '/login' : undefined" @click="showReportModal = true"><i-mdi-flag/> {{$t('report')}}</m-dropdown-item>
                </template>
            </m-dropdown>
        </m-flex>

        <m-form-modal v-model="showMoveThread" :title="$t('move')" @submit="moveThread">
            <m-select v-model="forumId" :label="$t('forum')" clearable :options="forums" @select-option="changeForum"/>
            <m-select v-model="categoryId" :label="$t('category')" :options="allowedCategories" clearable/>
        </m-form-modal>

        <NuxtPage :thread="thread"/>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Breadcrumb, ForumCategory, Game, Thread } from '~/types/models';

const { t } = useI18n();

const { user, hasPermission } = useStore();
const { isBanned, ban, gameBan } = storeToRefs(useStore());
const { public: config } = useRuntimeConfig();

const { data: thread } = await useResource<Thread>('thread', 'threads');

const showMoveThread = ref(false);
const showReportModal = ref(false);

const forumId = ref(thread.value.forum_id);
const categoryId = ref(thread.value.category_id);

const { data: categories } = await useWatchedFetchMany<ForumCategory>('forum-categories', {
    forum_id: forumId
});

const canCloseInCategory = computed(() => thread.value.category?.can_close_threads);
const threadGame = computed(() => thread.value.forum?.game);
const thumbnail = computed(() => {
    const avatar = thread.value.user?.avatar;
    if (avatar) {
        return `${config.storageUrl}/users/images/${avatar}`;
    } else {
        return `${config.siteUrl}/assets/default-avatar.webp`;
    }
});

useSeoMeta({
    ogSiteName: threadGame.value ? `ModWorkshop - ${threadGame.value.name} - Thread` : 'ModWorkshop - Thread',
	ogTitle: `${thread.value.name} by ${thread.value.user?.name ?? t('not_available')}`,
	ogImage: thumbnail.value,
	twitterCard: 'summary',
});

const canModerate = computed(() => hasPermission('manage-discussions', threadGame.value));
const canEdit = computed(() => canModerate.value || thread.value.user_id === user?.id);
const lockedReason = computed(() => {
    if (thread.value.locked_by_mod) {
        return t('cannot_comment_locked_by_mod');
    } else {
        return t('cannot_comment_locked');
    }
});

const breadcrumb = computed(() => {
    let crumbs: Breadcrumb[] = [
        { name: t('forum'), attachToPrev: 'forum' }
    ];

    if (threadGame.value) {
        crumbs.unshift({ name: t('games'), to: 'games' }, { name: threadGame.value.name, id: threadGame.value.short_name, type: 'game' });
    }

    if (thread.value.id && thread.value.category) {
        crumbs.push({ name: thread.value.category.name, id: thread.value.category.id, type: 'forum_category' });
    }

    crumbs.push({ name: thread.value.name, id: thread.value.id, type: 'thread' });

    return crumbs;
});

const { data: games } = await useFetchMany<Game>('games');
const forums = computed(() => {
    const forums = [{ id: 1, name: t('global_forum') }];

    if (games.value) {
        for (const game of games.value.data) {
            forums.push({ name: game.name, id: game.forum_id });
        }
    }

    return forums;
});

const allowedCategories = computed(() => {
    const canAppeal = ban.value?.can_appeal ?? true;
    const canAppealGame = gameBan.value?.can_appeal ?? true;

    return categories.value?.data.filter(cat => cat.can_post && (!isBanned.value || (canAppeal && canAppealGame))) ?? [];
});

function changeForum() {
    categoryId.value = allowedCategories.value[0]?.id;
}

async function pinThread(onError) {
    try {
        thread.value = await patchRequest(`threads/${thread.value.id}`, { pinned: !thread.value.pinned_at });
    } catch (error) {
        onError(error);
    }
}

async function lockThread(onError) {
    try {
        thread.value = await patchRequest(`threads/${thread.value.id}`, { locked: !thread.value.locked });
    } catch (error) {
        onError(error);
    }
}

async function closeThread(onError) {
    try {
        thread.value = await patchRequest(`threads/${thread.value.id}`, { closed: !thread.value.closed });
    } catch (error) {
        onError(error);
    }
}

async function moveThread(onError) {
    try {
        thread.value = await patchRequest(`threads/${thread.value.id}`, { forum_id: forumId.value, category_id: categoryId.value });
        showMoveThread.value = false;
    } catch (error) {
        onError(error);
    }
}
</script>