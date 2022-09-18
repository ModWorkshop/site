<template>
    <page-block>
        <the-breadcrumb :items="breadcrumb"/>
        <flex>
            <a-button v-if="canEdit" :to="`${thread.id}/edit`" icon="cog">{{$t('edit')}}</a-button>
            <a-button v-if="canModerate" icon="thumbtack" @click="pinThread">{{thread.pinned_at ? $t('unpin') : $t('pin')}}</a-button>
            <a-button v-if="canModerate" :disabled="thread.archived_by_mod && !canModerate" icon="box-archive" @click="archiveThread">{{thread.archived ? $t('unarchive') : $t('archive')}}</a-button>
            <a-report :url="`/threads/${thread.id}/reports`"/>
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
            :can-delete-all="canDeleteComments"
            :get-special-tag="commentSpecialTag"
            :can-comment="canComment"
            :cannot-comment-reason="cannotCommentReason"
        />
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Breadcrumb, Thread } from '~~/types/models';

const canEditComments = ref(false);
const canDeleteComments = ref(false);
const commentSpecialTag = ref(null);

const { user, hasPermission, isBanned, setGame } = useStore();

const { data: thread } = await useResource<Thread>('thread', 'threads');

setGame(thread.value.forum.game);

const canModerate = computed(() => hasPermission('edit-thread'));
const canEdit = computed(() => canModerate.value || thread.value.user_id === user?.id);

const canComment = computed(() => {
    if (isBanned || thread.value.user.blocked_me) {
        return false;
    }
    return !thread.value.archived || canModerate.value || thread.value.user_id === user?.id && !thread.value.archived_by_mod;
});

const cannotCommentReason = computed(() => {
    if (thread.value.archived) {
        return 'This thread has been archived.';
    }

    if (isBanned) {
        return 'Banned users cannot post comments';
    }

    if (thread.value.user.blocked_me) {
        return 'You cannot reply to the thread because the owner blocked you.';
    }
});

const breadcrumb = computed(() => {
    const forum = thread.value.forum;
    const game = forum.game;

    const crumb: Breadcrumb[] = [
        { name: 'forum', attachToPrev: 'forum' },
        { name: thread.value.name },
    ];

    if (game) {
        crumb.unshift({ name: game.name, id: game.short_name, type: 'game' });
    }

    return crumb;
});

async function pinThread() {
    thread.value = await usePatch(`threads/${thread.value.id}`, { pinned: !thread.value.pinned_at });
}

async function archiveThread() {
    thread.value = await usePatch(`threads/${thread.value.id}`, { archived: !thread.value.archived });
}
</script>