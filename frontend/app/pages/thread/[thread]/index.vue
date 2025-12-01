<template>
	<m-flex column gap="3">
		<div class="text-3xl break-words overflow-hidden">{{ thread.name }}</div>
		<m-content-block :padding="6">
			<m-flex>
				<NuxtLink class="mr-1 self-start" :to="`/user/${thread.user_id}`">
					<m-avatar class="align-middle" :src="thread.user?.avatar" size="lg"/>
				</NuxtLink>
				<m-flex column wrap class="overflow-hidden w-full">
					<m-flex class="items-center">
						<a-user :avatar="false" :user="thread.user"/>
						<NuxtLink class="text-body" :to="`/thread/${thread.id}`">
							<m-time :datetime="thread.created_at" relative/>
						</NuxtLink>
						<m-time v-if="thread.edited_at && thread.edited_at != thread.created_at" class="text-secondary" :datetime="thread.updated_at" :text="$t('edited')"/>
					</m-flex>
					<md-content class="w-full" :padding="2" allow-anchors :text="thread.content" :parser-version="thread.parser_version"/>
				</m-flex>
			</m-flex>
			<m-flex v-if="thread.tags?.length">
				<m-tag v-for="tag in thread.tags" :key="tag.id" :color="tag.color">{{ tag.name }}</m-tag>
			</m-flex>
		</m-content-block>
		<m-flex v-if="thread.answer_comment" column gap="4">
			<span class="h3">{{ $t('thread_answer') }}</span>
			<list-comment :comment="thread.answer_comment" :commentable="thread"/>
		</m-flex>
		<the-comments
			:url="`threads/${thread.id}/comments`"
			:page-url="`/thread/${thread.id}`"
			resource-name="replies"
			:can-edit-resource="canEditThread"
			:commentable="thread"
			:can-pin="canEditThread"
			:get-special-tag="commentSpecialTag"
			:can-comment="canComment"
			:cannot-comment-reason="cannotCommentReason"
			@mark-as-answer="markCommentAsAnswer"
		/>
	</m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Thread, Comment } from '~/types/models';

const { t } = useI18n();

const { user, hasPermission, isBanned, gameBan, ban } = useStore();
const { public: config } = useRuntimeConfig();

const { thread } = defineProps<{
	thread: Thread;
}>();

const showError = useQuickErrorToast();

const commentSpecialTag = function (comment: Comment) {
	if (comment.user_id === thread.user_id) {
		return `${t('poster')}`;
	}
};

const threadGame = computed(() => thread.forum?.game);
const canModerate = computed(() => hasPermission('manage-discussions', threadGame.value));
const canEditThread = computed(() => canModerate.value || (user ? user.id === thread.user_id : false));

const thumbnail = computed(() => {
	const avatar = thread.user?.avatar;
	if (avatar) {
		return `${config.storageUrl}/users/images/${avatar}`;
	} else {
		return `${config.siteUrl}/assets/default-avatar.webp`;
	}
});

useSeoMeta({
	ogSiteName: threadGame.value ? `ModWorkshop - ${threadGame.value.name} - Thread` : 'ModWorkshop - Thread',
	ogTitle: `${thread.name} by ${thread.user?.name ?? t('not_available')}`,
	ogImage: thumbnail.value,
	twitterCard: 'summary'
});

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

async function markCommentAsAnswer(comment: Comment) {
	try {
		if (thread.answer_comment_id === comment.id) {
			await patchRequest(`threads/${thread.id}`, {
				answer_comment_id: null
			});

			thread.answer_comment_id = null;
		} else {
			await patchRequest(`threads/${thread.id}`, {
				answer_comment_id: comment.id
			});

			thread.answer_comment_id = comment.id;
		}
	} catch (error) {
		showError(error);
	}
}
</script>
