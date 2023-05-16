<template>
    <page-block :game="threadGame" :breadcrumb="breadcrumb">
        <Title>{{thread.name}}</Title>
        <flex column>
            <the-tag-notices :tags="thread.tags"/>
        </flex>
        <a-alert v-if="thread.locked" color="warning" :desc="lockedReason"/>
        <flex>
            <a-button v-if="canEdit" :to="`${thread.id}/edit`" icon="mdi:cog">{{$t('edit')}}</a-button>
            <a-button v-if="canModerate" icon="thumbtack" @click="pinThread">{{thread.pinned_at ? $t('unpin') : $t('pin')}}</a-button>
            <a-button v-if="canEdit" :disabled="(thread.locked_by_mod && !canModerate)" :icon="thread.locked ? 'unlock' : 'lock'" @click="lockThread">
                {{thread.locked ? $t('unlock') : $t('lock')}}
            </a-button>
            <a-report resource-name="thread" :url="`/threads/${thread.id}/reports`"/>
        </flex>
        <div class="text-3xl">{{thread.name}}</div>
        <content-block :padding="4">
            <flex>
                <NuxtLink class="mr-1" :to="`/user/${thread.user_id}`">
                    <a-avatar class="align-middle" :src="thread.user.avatar" size="lg"/>
                </NuxtLink>
                <flex column wrap class="overflow-hidden w-full">
                    <flex>
                        <a-user :avatar="false" :user="thread.user"/>
                        <NuxtLink class="text-body" :to="`/thread/${thread.id}`">
                            <time-ago :time="thread.created_at"/>
                        </NuxtLink>
                        <span v-if="thread.updated_at != thread.created_at" class="text-secondary" :title="thread.updated_at">{{$t('edited')}}</span>
                    </flex>
                    <a-markdown class="mt-1" :text="thread.content"/>
                </flex>
            </flex>
            <flex v-if="thread.tags.length">
                <a-tag v-for="tag in thread.tags" :key="tag.id" :color="tag.color">{{tag.name}}</a-tag>
            </flex>
        </content-block>
        <the-comments
            :url="`threads/${thread.id}/comments`" 
            :page-url="`/thread/${thread.id}`"
            resource-name="replies"
            :commentable="thread" 
            :can-edit-all="canEditComments"
            :can-delete-all="canEditComments"
            :get-special-tag="commentSpecialTag"
            :can-comment="canComment"
            :cannot-comment-reason="cannotCommentReason"
        />
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, Thread } from '~~/types/models';
import { Comment } from '../../../types/models';

const { t } = useI18n();

const { user, hasPermission, isBanned, gameBan, ban } = useStore();
const { public: config } = useRuntimeConfig();

const { data: thread } = await useResource<Thread>('thread', 'threads');

const commentSpecialTag = function(comment: Comment) {
    if (comment.user_id === thread.value.user_id) {
        return `${t('poster')}`;
    } 
};

const threadGame = computed(() => thread.value.forum.game);
const canEditComments = computed(() => hasPermission('manage-discussions', threadGame.value));

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
const bannedCommenting = computed(() => {
    const canAppeal = ban?.can_appeal ?? true;
    const canAppealGame = gameBan?.can_appeal ?? true;

    return isBanned && (!thread.value.category?.banned_can_post || thread.value.user_id !== user!.id || !canAppeal || !canAppealGame);
});

const canComment = computed(() => {
    if (bannedCommenting.value || thread.value.user.blocked_me) {
        return false;
    }
    return !thread.value.locked || canModerate.value || (thread.value.user_id === user?.id && !thread.value.locked_by_mod);
});

const lockedReason = computed(() => {
    if (thread.value.locked_by_mod) {
        return t('cannot_comment_locked_by_mod');
    } else {
        return t('cannot_comment_locked');
    }
});

const cannotCommentReason = computed(() => {
    if (thread.value.locked) {
        return t('cannot_comment_locked');
    }

    if (bannedCommenting.value) {
        return t('cannot_comment_banned');
    }

    if (thread.value.user.blocked_me) {
        return t('cannot_comment_blocked');
    }
});

const breadcrumb = computed(() => {
    let crumbs: Breadcrumb[] = [
        { name: t('forum'), attachToPrev: 'forum' }
    ];

    if (threadGame.value) {
        crumbs.unshift({ name: threadGame.value.name, id: threadGame.value.short_name, type: 'game' });
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