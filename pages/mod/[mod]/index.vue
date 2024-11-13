<template>
    <m-flex gap="3" column>
        <m-flex column gap="3">
            <m-flex class="items-center">
                <span class="mod-title">{{mod.name}}</span>
                <mod-status class="ml-auto text-xl" :mod="mod"/>
            </m-flex>
        </m-flex>
        <div class="mod-main">
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
            :can-delete-all="canDeleteComments"
            :can-pin="canEdit"
            :get-special-tag="commentSpecialTag"
            :can-comment="canComment"
            :cannot-comment-reason="cannotCommentReason"
        />
    </m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import type { Mod, Comment } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod } from '~~/utils/mod-helpers';
const store = useStore();
const { t } = useI18n();

const { mod } = defineProps<{
    mod: Mod;
}>();

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
    font-size: 2rem;
}

.mod-main {
    display: grid;
    grid-gap: .75rem;
    margin-right: .75rem;
    grid-template-columns: 70% 30%;
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

@media (min-width:800px) and (max-width:1280px) {
    .mod-main {
        grid-template-columns: 55% 45%;
    }
}

@media (min-width:800px) {
    .mobile-banner {
        display: none;
    }
    
    .desktop-banner {
        display: block;
    }
}

@media (max-width:800px) {
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