<template>
    <flex column gap="3">
        <div class="text-3xl">{{thread.name}}</div>
        <content-block :padding="4">
            <flex>
                <NuxtLink class="mr-1 self-start" :to="`/user/${thread.user_id}`">
                    <a-avatar class="align-middle" :src="thread.user?.avatar" size="lg"/>
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
            <flex v-if="thread.tags?.length">
                <a-tag v-for="tag in thread.tags" :key="tag.id" :color="tag.color">{{tag.name}}</a-tag>
            </flex>
        </content-block>
        <the-comments
            :url="`threads/${thread.id}/comments`" 
            :page-url="`/thread/${thread.id}`"
            resource-name="replies"
            :commentable="thread" 
            :can-pin="canPin"
            :get-special-tag="commentSpecialTag"
            :can-comment="canComment"
            :cannot-comment-reason="cannotCommentReason"
        />
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Thread } from '~~/types/models';
import { Comment } from '../../../types/models';

const { t } = useI18n();

const { user, hasPermission, isBanned, gameBan, ban } = useStore();
const { public: config } = useRuntimeConfig();

const { thread } = defineProps<{
   thread: Thread 
}>();

const commentSpecialTag = function(comment: Comment) {
    if (comment.user_id === thread.user_id) {
        return `${t('poster')}`;
    } 
};

const threadGame = computed(() => thread.forum?.game);
const canPin = computed(() => user ? user.id === thread.user_id : false);

const thumbnail = computed(() => {
    const avatar = thread.user?.avatar;
    if (avatar) {
        return `${config.storageUrl}/users/images/${avatar}`;
    } else {
        return `${config.siteUrl}/assets/no-preview-dark.png`;
    }
});

useServerSeoMeta({
    ogSiteName: threadGame.value ? `ModWorkshop - ${threadGame.value.name} - Thread` : 'ModWorkshop - Thread',
	ogTitle: `${thread.name} by ${thread.user?.name ?? t('not_available')}`,
	ogImage: thumbnail.value,
	twitterCard: 'summary',
});

const canModerate = computed(() => hasPermission('manage-discussions', threadGame.value));
const bannedCommenting = computed(() => {
    const canAppeal = ban?.can_appeal ?? true;
    const canAppealGame = gameBan?.can_appeal ?? true;

    return isBanned && (!thread.category?.banned_can_post || thread.user_id !== user!.id || !canAppeal || !canAppealGame);
});

const canComment = computed(() => {
    if (bannedCommenting.value || thread.user?.blocked_me) {
        return false;
    }
    return !thread.locked || canModerate.value || (thread.user_id === user?.id && !thread.locked_by_mod);
});

const cannotCommentReason = computed(() => {
    if (thread.locked) {
        return t('cannot_comment_locked');
    }

    if (bannedCommenting.value) {
        return t('cannot_comment_banned');
    }

    if (thread.user?.blocked_me) {
        return t('cannot_comment_blocked');
    }
});
</script>