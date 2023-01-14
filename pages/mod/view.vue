<template>
    <mod-page v-if="mod" :mod="mod">
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
    </mod-page>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod, Comment } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod, memberLevels } from '~~/utils/mod-helpers';

const store = useStore();

const { t } = useI18n();

const { data: mod } = await useResource<Mod>('mod', 'mods', {
    suspended: t('error_suspended')
});

definePageMeta({  
    key: route => route.fullPath
});

if (mod.value) {
    usePost(`mods/${mod.value.id}/register-view`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                mod.value.views++;
            }
        }
    });
}

const canEdit = computed(() => canEditMod(mod.value));
const canEditComments = computed(() => store.hasPermission('manage-discussions', mod.value.game));
const canDeleteComments = computed(() => canEditComments.value || (canEdit.value && store.hasPermission('delete-own-mod-comments', mod.value.game)));
const canComment = computed(() => !mod.value.user.blocked_me && !store.isBanned && (!mod.value.comments_disabled || canEdit.value));
const cannotCommentReason = computed(() => {
    if (mod.value.comments_disabled) {
        return t('comments_disabled');
    }

    if (store.isBanned) {
        return t('cannot_comment_banned');
    }

    if (mod.value.user.blocked_me) {
        return t('cannot_comment_blocked_mod');
    }
});

function commentSpecialTag(comment: Comment) {
    if (comment.user_id === mod.value.user_id) {
        return `${t('owner')}`;
    } else {
        const member = mod.value.members.find(member => comment.user_id === member.id);
        if (member) {
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