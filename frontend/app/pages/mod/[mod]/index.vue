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
					</template>
				</m-dropdown>
			</m-flex>
		</m-flex>
		<div class="mod-main mb-3">
			<m-flex class="overflow-x-hidden" column gap="3">
				<mod-banner class="desktop-banner" :mod="mod"/>
				<mod-tabs :mod="mod"/>
			</m-flex>
			<mod-banner class="mobile-banner" :mod="mod"/>
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
const store = useStore();
const { t } = useI18n();
const { public: config } = useRuntimeConfig();

const { mod } = defineProps<{
	mod: Mod;
}>();

const showReportModal = ref(false);
const canEdit = computed(() => canEditMod(mod));
const canDeleteComments = computed(() => canEdit.value && store.hasPermission('delete-own-mod-comments', mod.game));
const canComment = computed(() => !mod.user?.blocked_me && !store.isBanned && (!mod.comments_disabled || canEdit.value));
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
</script>

<style>
.large-button {
	font-size: 1.15rem;
	padding: 1rem !important;
	text-align: center;
}
</style>

<style scoped>
.mod-title {
	font-size: 1.5rem;
	font-weight: 500;
}

.mod-main {
	display: grid;
	grid-gap: 0.75rem;
	margin-right: 0.75rem;
	grid-template-columns: 66.5% 33.5%;
}

@media (min-width: 600px) and (max-width: 850px) {
	.mod-info .thumbnail {
		display: none;
	}
}

@media (min-width: 1280px) and (max-width: 1500px) {
	.mod-main {
		grid-template-columns: 60% 40%;
	}
}

@media (min-width: 800px) and (max-width: 1280px) {
	.mod-main {
		grid-template-columns: 55% 45%;
	}
}

@media (min-width: 800px) {
	.mobile-banner {
		display: none;
	}

	.desktop-banner {
		display: block;
	}
}

@media (max-width: 800px) {
	.mobile-banner {
		display: block;
	}

	.desktop-banner {
		display: none;
	}

	.mod-info-holder {
		order: -1;
	}

	.mod-banner {
		order: -1;
	}

	.mod-main {
		grid-template-columns: auto;
		margin-right: 0;
		gap: 1px;
	}
}

.mobile-banner {
	margin-bottom: 1rem;
}
</style>
