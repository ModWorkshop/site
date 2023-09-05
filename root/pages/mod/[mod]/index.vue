<template>
    <flex gap="3" column>
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
            :can-pin="canEdit"
            :get-special-tag="commentSpecialTag"
            :can-comment="canComment"
            :cannot-comment-reason="cannotCommentReason"
        />
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod, Comment, ModMember } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod } from '~~/utils/mod-helpers';
const store = useStore();
const { t } = useI18n();

const { mod } = defineProps<{
    mod: Mod;
}>();

const canEdit = computed(() => canEditMod(mod));
const canEditComments = computed(() => store.hasPermission('manage-discussions', mod.game));
const canDeleteComments = computed(() => canEditComments.value || (canEdit.value && store.hasPermission('delete-own-mod-comments', mod.game)));
const canComment = computed(() => !mod.user.blocked_me && !store.isBanned && (!mod.comments_disabled || canEdit.value));
const cannotCommentReason = computed(() => {
    if (mod.comments_disabled) {
        return t('comments_disabled');
    }

    if (store.isBanned) {
        return t('cannot_comment_banned');
    }

    if (mod.user.blocked_me) {
        return t('cannot_comment_blocked_mod');
    }
});

function commentSpecialTag(comment: Comment) {
    if (comment.user_id === mod.user_id) {
        return `${t('owner')}`;
    } else {
        const member: ModMember = mod.members.find(member => comment.user_id === member.id);
        if (member && member.accepted) {
            return t(`member_level_${member.level}`);
        }
    }
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

@media (min-width:1280px) and (max-width:1500px) {
    .mod-main {
        grid-template-columns: 60% 40%;
    }
}

@media (max-width:1280px) {
    .mod-banner {
        height: 150px !important;
    }

    .mod-info-holder {
        order: -1;
    }

    .mod-main {
        grid-template-columns: auto;
        margin-right: 0;
        gap: 1px;
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