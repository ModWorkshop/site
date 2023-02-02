<template>
    <content-block :alt-background="isReply" :gap="3" :padding="3" :class="classes">
        <flex class="comment-body">
            <NuxtLink class="mr-1" :to="`/user/${comment.user_id}`">
                <a-avatar class="align-middle" :src="comment.user?.avatar" size="md"/>
            </NuxtLink>
            <flex column wrap class="overflow-hidden w-full">
                <flex wrap>
                    <a-user :avatar="false" :user="comment.user"/>
                    <span v-if="specialTag" class="text-success">({{specialTag}})</span>
                    <NuxtLink class="ml-1 text-secondary" :to="commentPage">
                        <time-ago :time="comment.created_at"/>
                    </NuxtLink>
                    <span v-if="comment.updated_at != comment.created_at" class="text-secondary" :title="comment.updated_at">{{$t('edited')}}</span>
                    <a-icon v-if="comment.pinned" class="transform rotate-45" icon="thumbtack" :title="$t('pinned')"/>
                </flex>
                <a-markdown class="mt-1" :text="content"/>
            </flex>
            <div class="float-right">
                <flex class="comment-actions text-body" :style="{visibility: areActionsVisible ? 'visible' : null}">
                    <a-button v-if="canReply" class="cursor-pointer" title="Reply" icon="reply" size="sm" @click="$emit('reply', comment)"/>
                    <a-button
                        v-if="!isReply"
                        class="cursor-pointer"
                        size="sm"
                        :title="comment.subscribed ? $t('unsubscribe') : $t('subscribe')"
                        :icon="comment.subscribed ? 'bell-slash' : 'bell'"
                        @click="subscribe"
                    />
                    <VDropdown v-model:shown="areActionsVisible" style="margin: 0; border: 0;">
                        <a-button class="cursor-pointer" icon="ellipsis-h" size="sm"/>
                        <template #popper>
                            <a-dropdown-item v-if="canEdit" @click="$emit('edit', comment)">{{$t('edit')}}</a-dropdown-item>
                            <a-dropdown-item v-if="!isReply && canEditAll" @click="togglePinnedState">{{comment.pinned ? $t('unpin') : $t('pin')}}</a-dropdown-item>
                            <a-dropdown-item v-if="canDeleteAll" @click="openDeleteModal">{{$t('delete')}}</a-dropdown-item>
                            <a-report resource-name="comment" :url="`${url}/${comment.id}/reports`">
                                <a-dropdown-item>{{$t('report')}}</a-dropdown-item>
                            </a-report>
                        </template>
                    </VDropdown>
                </flex>
            </div>
        </flex>
        <div v-if="replies?.data?.length" class="mx-3">
            <flex column>
                <a-comment v-for="reply of replies.data" 
                    :key="reply.id"
                    :url="url"
                    :page-url="pageUrl"
                    :comment="reply"
                    :can-comment="canReply"
                    :can-edit-all="canEditAll"
                    :can-delete-all="canDeleteAll"
                    :current-focus="currentFocus"
                    :get-special-tag="getSpecialTag"
                    :commentable="commentable"
                    is-reply
                    @edit="() => $emit('edit', reply)"
                    @reply="$emit('reply', comment, reply.user)"
                    @delete="deleteComment"
                />
            </flex>
            <a-pagination 
                v-if="fetchReplies && replies.meta" 
                v-model="page" 
                class="my-2"
                :total="replies.meta.total" 
                :per-page="replies.meta.per_page" 
                @update="loadReplies"
            />
            <NuxtLink v-else-if="!fetchReplies && comment.total_replies && comment.total_replies > 3" class="my-2" :to="commentPage">
                {{$t('read_all_replies')}} ({{$t('replies_n', comment.total_replies)}})
            </NuxtLink>
        </div>
    </content-block>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Comment, Game, User } from '~~/types/models';
const YesNoModal = useYesNoModal();

const props = defineProps<{
    comment: Comment,
    url: string,
    pageUrl: string,
    canComment: boolean,
    canEditAll: boolean,
    canDeleteAll: boolean,
    isReply?: boolean,
    commentable: { id: number, game?: Game },
    getSpecialTag: (comment: Comment) => string,
    currentFocus: Comment,
    fetchReplies?: boolean
}>();

const { t } = useI18n();
const specialTag = computed(() => props.getSpecialTag && props.getSpecialTag(props.comment));
const content = toRef(props.comment, 'content');

const page = ref(1);
let { data: replies, refresh: loadReplies } = await useFetchMany<Comment>(() => props.fetchReplies ? `${props.url}/${props.comment.id}/replies` : '', {
    immediate: props.fetchReplies, 
    params: reactive({ page, limit: 20 })
});

if (!props.fetchReplies) {    
    replies = ref({
        data: props.comment.last_replies,
        meta: null
    });
}

content.value = content.value.replace(/<@([0-9]+)>/g, (match, id) => {
    const user = props.comment.mentions.find(user => user.id == id);

    if (user) {
        return `@${user.unique_name}`;
    } else {
        return `<\\@${id}>`;
    }
});

const emit = defineEmits([
    'reply',
    'delete',
    'pin',
    'edit'
]);

const { user, hasPermission } = useStore();

const areActionsVisible = ref(false);
const canEdit = computed(() => user && (hasPermission('create-discussions', props.commentable.game) && user.id === props.comment.user_id) || props.canEditAll);
// const canReport = computed(() => false);
const canReply = computed(() => props.canComment && !props.comment.user?.blocked_me);

const classes = computed(() => ({
    comment: true,
    reply: props.isReply,
    focus: props.currentFocus && props.currentFocus.id == props.comment.id
}));

const commentPage = computed(() => {
    if (props.comment.reply_to) {
        return `${props.pageUrl}/post/${props.comment.reply_to}`;
    } else {
        return `${props.pageUrl}/post/${props.comment.id}`;
    }
});

async function subscribe() {
    try {
        const url = `${props.url}/${props.comment.id}`;
        if (props.comment.subscribed) {
            await useDelete(`${url}/subscription`);
        } else {
            await usePost(`${url}/subscription`);
        }
        props.comment.subscribed = !props.comment.subscribed;
    } catch (error) {
        console.log(error);
    }
}

async function togglePinnedState() {
    props.comment.pinned = !props.comment.pinned;
    emit('pin', props.comment);
}

//Deletes a reply in the comment
function deleteComment(comment: Comment, isReply: boolean) {
    const replies = props.comment.last_replies;
    remove(replies, comment);
    emit('delete', comment, isReply);
}

function openDeleteModal() {
    YesNoModal({
        title: t('are_you_sure'),
        desc: t('delete_comment_desc'),
        async yes() {
            //If it's a reply, it will call its parent comment's deleteComment function and then call the actual holder of the comments.
            emit('delete', props.comment, props.isReply);
        }
    });    
}
</script>

<style>
.comment {
    transition: border, background-color 0.5s cubic-bezier(0.230, 1.000, 0.320, 1.000);
}
.reply {
    background-color: var(--alt-content-bg-color);
}

.focus {
    background-color: #ffd4001a !important;
    border-left: solid 3px #ffd400;
}

.comment-body .comment-actions {
    visibility: hidden
}

.comment-body:hover .comment-actions {
    visibility: visible
}
</style>