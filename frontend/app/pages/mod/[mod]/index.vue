<template>
	<m-flex gap="3" column class="mt-2">
		<m-flex gap="3" wrap>
			<m-flex class="items-center" wrap>
				<mod-status class="text-2xl mt-1" :mod="mod"/>
				<span class="mod-title">{{ mod.name }}</span>
			</m-flex>

			<m-flex class="ml-auto mb-auto">
				<m-dropdown :disabled="!!mod.followed" align="end">
					<m-button @click="mod.followed && setFollowMod(mod, false)">
						{{ $t(mod.followed ? 'unfollow' : 'follow') }} <i-mdi-chevron-down/>
					</m-button>
					<template #content>
						<m-dropdown-item @click="setFollowMod(mod, true)">
							<i-mdi-bell/> {{ $t('follow_mod_notifs') }}
						</m-dropdown-item>
						<m-dropdown-item @click="setFollowMod(mod, false)">
							<i-mdi-plus/> {{ $t('follow') }}
						</m-dropdown-item>
					</template>
				</m-dropdown>

				<report-modal v-model:show-modal="showReportModal" resource-name="mod" :url="`/mods/${mod.id}/reports`"/>

				<m-dropdown align="end">
					<m-button>
						<i-mdi-dots-vertical/>
					</m-button>
					<template #content>
						<m-dropdown-item @click="copyLink">
							<i-mdi-link/> {{ $t('copy_link') }}
						</m-dropdown-item>
						<div class="dropdown-splitter"/>
						<m-dropdown-item :to="!store.user ? '/login' : undefined" @click="showReportModal = true"><i-mdi-flag/> {{ $t('report') }}</m-dropdown-item>
						<m-dropdown-item v-if="store.user" @click="setIgnoreMod(mod)">
							<i-mdi-eye v-if="mod.ignored"/>
							<i-mdi-eye-off v-else/>
							{{ $t(mod.ignored ? 'unignore' : 'ignore') }}
						</m-dropdown-item>
						<m-dropdown-item v-if="user && mod.members.find(member => member.id == user?.id)" @click="leaveMembers">
							<i-mdi-exit-to-app/> {{ $t('leave_members') }}
						</m-dropdown-item>
					</template>
				</m-dropdown>
			</m-flex>
		</m-flex>
		<div class="mod-banner-thumb">
			<mod-banner :mod="mod"/>
			<mod-thumbnail :thumbnail="mod.thumbnail" prefer-hq/>
		</div>
		<div class="mod-main">
			<mod-tabs :mod="mod"/>
			<mod-right-pane :mod="mod"/>
		</div>
		<the-comments
			lazy
			:url="`mods/${mod.id}/comments`"
			:page-url="`/mod/${mod.id}`"
			:commentable="mod"
			:can-edit-resource="canEdit"
			:can-delete-all="canDeleteComments"
			:can-pin="canEdit"
			:get-special-tag="commentSpecialTag"
			:can-comment="canComment"
			:cannot-comment-reason="cannotCommentReason"
		/>
	</m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Mod, Comment } from '~/types/models';
import { useI18n } from 'vue-i18n';
import { remove } from '@antfu/utils';
const store = useStore();
const { user } = useStore();
const { t } = useI18n();
const { public: config } = useRuntimeConfig();
const showToast = useQuickErrorToast();
const yesNoModal = useYesNoModal();

const { mod } = defineProps<{
	mod: Mod;
}>();

const showReportModal = ref(false);
const canEdit = computed(() => canEditMod(mod));
const canDeleteComments = computed(() => canEdit.value && store.hasPermission('delete-own-mod-comments', mod.game));
const canComment = computed(() => {
	if (canEdit.value) {
		return true;
	}

	return !mod.user?.blocked_me && !store.isBanned && !mod.comments_disabled;
});

const cannotCommentReason = computed(() => {
	if (mod.comments_disabled) {
		return t('comments_disabled');
	}

	if (store.isBanned) {
		return t('cannot_comment_banned');
	}

	if (mod.user?.blocked_me) {
		return t('cannot_comment_blocked_mod');
	}
});

function copyLink() {
	navigator.clipboard.writeText(`${config.siteUrl}/mod/${mod.id}`);
}

function commentSpecialTag(comment: Comment) {
	if (comment.user_id === mod.user_id) {
		return `${t('owner')}`;
	} else {
		const member = mod.members.find(member => comment.user_id === member.id);
		if (member && member.accepted) {
			return t(`member_level_${member.level}`);
		}
	}
}

async function leaveMembers() {
	if (!user) return;
	yesNoModal({
		title: t('are_you_sure'),
		desc: t('irreversible_action'),
		async yes() {
			try {
				await deleteRequest(`mods/${mod.id}/members/${user.id}`);
				remove(mod.members, mod.members.find(member => member.id === user.id));
			} catch (error) {
				showToast(error);
			}
		}
	});
}
</script>
<style scoped>
.mod-title {
	font-size: 1.5rem;
	font-weight: 500;
}
</style>

<style>
.mod-banner-thumb .mod-thumbnail {
	margin: 0 auto 0 auto;
	width: 500px !important;
}

.large-button {
	font-size: 1.15rem;
	padding: 1rem !important;
	text-align: center;
}

.mod-banner-thumb {
	display: grid;
	grid-gap: 0.75rem;
	margin-right: 0.75rem;
	grid-template-columns: 66.5% 33.5%;
}

.mod-main {
	display: grid;
	grid-gap: 0.75rem;
	margin-right: 0.75rem;
	grid-template-columns: 66.66% 33.33%;
}

.downloads-list {
	display: none;
}

@media (max-width: 800px) {
	.mod-banner-thumb {
		display: flex;
		flex-direction: column;
	}
}

@media (max-width: 1024px) {
	.downloads-table {
		display: table;
	}
	.downloads-list {
		display: none;
	}

	.mod-info-holder {
		order: -1;
	}

	.mod-main {
		grid-template-columns: auto;
		margin-right: 0;
	}
}

@media (max-width: 800px) {
	.downloads-table {
		display: none;
	}
	.downloads-list {
		display: flex;
	}
}
</style>
