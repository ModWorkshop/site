<template>
    <flex column gap="3">
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
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod, Comment } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod, memberLevels } from '~~/utils/mod-helpers';

const store = useStore();

const { t } = useI18n();

const props = defineProps<{
    mod: Mod
}>();

if (props.mod) {
    usePost(`mods/${props.mod.id}/register-view`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                props.mod.views++;
            }
        }
    });
}

const canEdit = computed(() => canEditMod(props.mod));
const canEditComments = computed(() => store.hasPermission('manage-discussions', props.mod.game));
const canDeleteComments = computed(() => canEditComments.value || (canEdit.value && store.hasPermission('delete-own-mod-comments', props.mod.game)));
const canComment = computed(() => !props.mod.user.blocked_me && !store.isBanned && (!props.mod.comments_disabled || canEdit.value));
const cannotCommentReason = computed(() => {
    if (props.mod.comments_disabled) {
        return t('comments_disabled');
    }

    if (store.isBanned) {
        return t('cannot_comment_banned');
    }

    if (props.mod.user.blocked_me) {
        return t('cannot_comment_blocked_mod');
    }
});

function commentSpecialTag(comment: Comment) {
    if (comment.user_id === props.mod.user_id) {
        return `${t('owner')}`;
    } else {
        const member = props.mod.members.find(member => comment.user_id === member.id);
        if (member) {
            return memberLevels[member.level];
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