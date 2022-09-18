<template>
    <page-block>
        <Head>
            <Title>{{mod.name}}</Title>
        </Head>
        <the-breadcrumb :items="mod.breadcrumb"/>
        <flex column gap="2">
            <a-alert v-for="notice of notices" :key="notice.id" :color="notice.type" :desc="notice.localized ? $t(notice.notice) : notice.notice"/>
            <a-alert v-if="mod.suspended" color="danger" :title="$t('suspended')">
                <i18n-t keypath="mod_suspended" tag="span">
                    <template #reason>
                        <span v-if="mod.last_suspension">
                            {{mod.last_suspension.reason}}
                        </span>
                        <span v-else>
                            No reason stated.
                        </span>
                    </template>
                    <template #rules>
                        <NuxtLink to="/rules">{{$t('rules').toLowerCase()}}</NuxtLink>
                    </template>
                    <template #forum>
                        <NuxtLink :to="`/game/${mod.game?.short_name}/forum?category=appeals`">{{$t('forum').toLowerCase()}}</NuxtLink>
                    </template>
                </i18n-t>
            </a-alert>
        </flex>

        <a-alert v-if="!mod.has_download" color="warning" :title="$t('files_alert_title')" :desc="$t('files_alert')"/>
        <a-alert v-else-if="mod.approved === null" color="info" :title="$t('mod_waiting')">
            <span>
                {{$t('mod_waiting_desc')}}
            </span>
            <mod-approve v-if="canManage" :mod="mod"/>
        </a-alert>
        <a-alert v-else-if="mod.approved === false" color="danger" :title="$t('mod_rejected')" :desc="$t('mod_rejected_desc')"/>
        <flex>
            <a-button v-if="canEdit" :to="`/mod/${mod.id}/edit`" icon="cog">{{$t('edit_mod')}}</a-button>
            <a-report :url="`/mods/${mod.id}/reports`"/>
            <Popper :disabled="mod.followed">
                <a-button :icon="mod.followed ? 'minus' : 'plus'" @click="mod.followed && setFollowMod(mod, false)">
                    {{$t(mod.followed ? 'unfollow' : 'follow')}} <font-awesome-icon v-if="!mod.followed" icon="caret-down"/>
                </a-button>
                <template #content>
                    <a-dropdown-item @click="setFollowMod(mod, true)">Follow and get notified for updates</a-dropdown-item>
                    <a-dropdown-item @click="setFollowMod(mod, false)">{{$t('follow')}}</a-dropdown-item>
                </template>
            </Popper>
            <a-button icon="share-nodes" @click="openShare">{{$t('share')}}</a-button>
            <Popper v-if="canManage" arrow>
                <a-button icon="gavel">{{$t('moderation')}}</a-button>
                <template #content>
                    <mod-suspend :mod="mod">
                        <a-dropdown-item>{{mod.suspended ? $t('unsuspend') : $t('suspend')}}</a-dropdown-item>
                    </mod-suspend>
                    <a-dropdown-item v-if="mod.images.length" @click="deleteAllImages">{{$t('delete_images')}}</a-dropdown-item>
                    <a-dropdown-item v-if="mod.files.length" @click="deleteAllFiles">{{$t('delete_files')}}</a-dropdown-item>
                </template>
            </Popper>
        </flex>
        <div>
            <mod-banner :mod="mod"/>
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
            :can-edit-all="canEditComments"
            :can-delete-all="canDeleteComments"
            :get-special-tag="commentSpecialTag"
            :can-comment="canComment"
            :cannot-comment-reason="cannotCommentReason"
        />
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod, Comment } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod, memberLevels } from '~~/utils/mod-helpers';
import { setFollowMod } from '~~/utils/follow-helpers';

const { hasPermission, isBanned } = useStore();
const { public: config } = useRuntimeConfig();

const { t } = useI18n();

const { data: mod } = await useResource<Mod>('mod', 'mods', {
    suspended: t('suspended_error')
});

const yesNoModal = useYesNoModal();

if (mod.value) {
    usePost(`mods/${mod.value.id}/register-view`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                mod.value.views++;
            }
        }
    });
}

const notices = computed(() => {
    const notices: { id: number, type: string, notice: string, localized: boolean }[] = [];
    for (const tag of mod.value.tags) {
        if (tag.notice && tag.notice.length > 0 && notices.length < 2) {
            notices.push({ id: tag.id, type: tag.notice_type, notice: tag.notice, localized: tag.notice_localized });
        }
    }

    return notices;
});

const canEdit = computed(() => canEditMod(mod.value));
const canEditComments = computed(() => hasPermission('edit-comment'));
const canManage = computed(() => hasPermission('manage-mod'));
const canDeleteComments = computed(() => canEditComments.value || (canEdit.value && hasPermission('delete-own-mod-comment')));
const canComment = computed(() => !mod.value.user.blocked_me && !isBanned && (!mod.value.comments_disabled || canEdit.value));
const cannotCommentReason = computed(() => {
    if (mod.value.comments_disabled) {
        return t('comments_disabled');
    }

    if (isBanned) {
        return 'Banned users cannot post comments';
    }

    if (mod.value.user.blocked_me) {
        return 'You cannot comment on the mod because the owner blocked you.';
    }
});

function commentSpecialTag(comment: Comment) {
    if (comment.user_id === mod.value.user_id) {
        return `${t('owner')}`;
    } else {
        const member = mod.value.members.find(member => comment.user_id === member.id);
        if (member) {
            return memberLevels[member.level];
        }
    }
}

function openShare() {
    navigator.share({
        url: `${config.siteUrl}/${mod.value.id}`
    });
}

function deleteAllFiles() {
    yesNoModal({
        desc: 'This will delete all files of the mod, this cannot be reversed!',
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${mod.value.id}/files`);
            mod.value.files = [];
            mod.value.has_download = mod.value.links.length > 0;
        }
    });
}

function deleteAllImages() {
    yesNoModal({
        desc: 'This will delete all images of the mod, this cannot be reversed!',
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${mod.value.id}/images`);
            mod.value.images = [];
        }
    });
}
</script>

<style scoped>
.mod-main {
    display: grid;
    grid-gap: .75rem;
    margin-right: .75rem;
    grid-template-columns: 70% 30%
}

@media (min-width:600px) and (max-width:850px) {
    .mod-info .thumbnail {
        display: none;
    }
}

@media (max-width:850px) {
    .mod-banner {
        height: 295px;
    }

    .mod-info {
        order: -1;
    }

    .mod-main {
        grid-template-columns: auto;
        margin-right: 0;
    }
    .contributor-block .info{
        line-height: 32px;
    }
    .contributor-block .avatar {
        height: 64px;
        width: 64px;
    }
}
</style>