<template>
    <page-block :game="threadGame" :breadcrumb="breadcrumb">
        <Title>{{thread.name}}</Title>
        <the-tag-notices v-if="thread.tags" :tags="thread.tags"/>
        <a-alert v-if="thread.locked" color="warning" :desc="lockedReason"/>
        <flex>
            <NuxtLink v-if="$route.name == 'thread-thread-edit'" :to="`/thread/${thread.id}`">
                <a-button icon="arrow-left">{{$t('return_to_thread')}}</a-button>
            </NuxtLink> 
            <a-button v-else-if="canEdit" :to="`/thread/${thread.id}/edit`" icon="mdi:cog">{{$t('edit')}}</a-button>
            <a-button v-if="canModerate" icon="thumbtack" @click="pinThread">{{thread.pinned_at ? $t('unpin') : $t('pin')}}</a-button>
            <a-button v-if="canEdit" :disabled="(thread.locked_by_mod && !canModerate)" :icon="thread.locked ? 'unlock' : 'lock'" @click="lockThread">
                {{thread.locked ? $t('unlock') : $t('lock')}}
            </a-button>
            <a-report resource-name="thread" :url="`/threads/${thread.id}/reports`"/>
        </flex>
        <NuxtPage :thread="thread"/>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, Thread } from '~~/types/models';

const { t } = useI18n();

const { user, hasPermission } = useStore();
const { public: config } = useRuntimeConfig();

const { data: thread } = await useResource<Thread>('thread', 'threads');

const threadGame = computed(() => thread.value.forum.game);

const thumbnail = computed(() => {
    const avatar = thread.value.user.avatar;
    if (avatar) {
        return `${config.storageUrl}/users/images/${avatar}`;
    } else {
        return `${config.siteUrl}/assets/no-preview-dark.png`;
    }
});

useServerSeoMeta({
    ogSiteName: threadGame.value ? `ModWorkshop - ${threadGame.value.name} - Thread` : 'ModWorkshop - Thread',
	ogTitle: `${thread.value.name} by ${thread.value.user.name}`,
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

async function pinThread() {
    thread.value = await patchRequest(`threads/${thread.value.id}`, { pinned: !thread.value.pinned_at });
}

async function lockThread() {
    thread.value = await patchRequest(`threads/${thread.value.id}`, { locked: !thread.value.locked });
}
</script>