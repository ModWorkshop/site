<template>
    <content-block :alt-background="isReply" :gap="0" :padding="3" :class="{comment: true, reply: isReply, focus: currentFocus && currentFocus.id == comment.id}">
        <flex class="comment-body" gap="2">
            <NuxtLink class="mr-1" :to="`/user/${comment.user_id}`">
                <a-avatar class="align-middle" :src="comment.user.avatar" size="md"/>
            </NuxtLink>
            <flex column wrap class="overflow-hidden w-full">
                <flex>
                    <a-user :avatar="false" :user="comment.user"/>
                    <span v-if="specialTag" class="text-success">({{specialTag}})</span>
                    <NuxtLink class="ml-1 text-secondary" :to="`/post/${comment.id}`">
                        <time-ago :time="comment.created_at"/>
                    </NuxtLink>
                    <span v-if="comment.updated_at != comment.created_at" class="text-secondary" :title="comment.updated_at">{{$t('edited')}}</span>
                    <font-awesome-icon v-if="comment.pinned" class="transform rotate-45" icon="thumbtack" :title="$t('pinned')"/>
                </flex>
                <a-markdown class="mt-1" :text="content"/>
            </flex>
            <div class="float-right">
                <flex class="comment-actions text-body" :style="{visibility: areActionsVisible ? 'visible' : null}">
                    <font-awesome-icon v-if="canReply" class="cursor-pointer" title="Reply" icon="reply" @click="$emit('reply', comment)"/>
                    <font-awesome-icon v-if="!isReply" class="cursor-pointer" :title="comment.subbed ? $t('unsubscribe') : $t('subscribe')" :icon="comment.subbed ? 'slash' : 'bell'"/>
                    <Popper arrow style="margin: 0; border: 0;" @open:popper="setActionsVisible(true)" @close:popper="setActionsVisible(false)">
                        <font-awesome-icon class="cursor-pointer" icon="ellipsis-h"/>
                        <template #content>
                            <a-dropdown-item v-if="canEdit" @click="$emit('edit', comment)">{{$t('edit')}}</a-dropdown-item>
                            <a-dropdown-item v-if="!isReply && canEditAll" @click="togglePinnedState">{{comment.pinned ? $t('unpin') : $t('pin')}}</a-dropdown-item>
                            <a-dropdown-item v-if="canDeleteAll" @click="openDeleteModal">{{$t('delete')}}</a-dropdown-item>
                            <a-dropdown-item>{{$t('report')}}</a-dropdown-item>
                        </template>
                    </Popper>
                </flex>
            </div>
        </flex>
        <template v-if="!isReply">
            <flex v-if="comment.last_replies.length > 0" column class="replies px-6 mt-2">
                <a-comment v-for="reply of comment.last_replies" 
                    :key="reply.id"
                    :comment="reply"
                    :can-edit-all="canEditAll"
                    :can-delete-all="canDeleteAll"
                    :current-focus="currentFocus"
                    :get-special-tag="getSpecialTag"
                    is-reply
                    @edit="() => $emit('edit', reply)"
                    @reply="$emit('reply', comment, reply.user)"
                    @delete="deleteComment"
                />
            </flex>
            <div v-if="comment.total_replies > 3" class="mx-4 mb-3">
                <a class="load-more-replies cursor-pointer">{{$t('load_more')}} ({{comment.total_replies}} {{$t('replies')}})</a>
            </div>
        </template>
    </content-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Comment, User } from '~~/types/models';
const { init: openModal } = useModal();

const props = defineProps<{
    comment: Comment,
    canEditAll: boolean,
    canDeleteAll: boolean,
    isReply?: boolean,
    getSpecialTag: (comment: Comment) => string,
    currentFocus: Comment
}>();

const specialTag = computed(() => props.getSpecialTag && props.getSpecialTag(props.comment));
const content = toRef(props.comment, 'content');
content.value = content.value.replace(/<@([0-9]+)>/g, (match, id) => {
    const user: User = props.comment.mentions.find(user => user.id == id);

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
const canEdit = computed(() => user && (hasPermission('edit-own-comment') && user.id === props.comment.user_id) || props.canEditAll);
// const canReport = computed(() => false);
const canReply = computed(() => true);

async function togglePinnedState() {
    props.comment.pinned = !props.comment.pinned;
    emit('pin', props.comment);
}

function setActionsVisible(visible: boolean) {
    areActionsVisible.value = visible;
}

//Deletes a reply in the comment
function deleteComment(commentId: number, isReply: boolean) {
    const lastReplies = props.comment.last_replies;
    lastReplies.splice(lastReplies.findIndex(com => com.id === commentId), 1);
    emit('delete', commentId, isReply);
}

function openDeleteModal() {
    openModal({
        message: 'This will delete the comment',
        onOk() {
            emit('delete', props.comment.id, props.isReply);
        }
    });

    //If it's a reply, it will call its parent comment's deleteComment function and then call the actual holder of the comments.
    
}
</script>

<style>
    .comment {
        transition: border, background-color 0.5s cubic-bezier(0.230, 1.000, 0.320, 1.000);
    }
    .reply {
        background-color: #22262a;
    }

    .focus {
        background-color: #ffd4001a !important;
        border-left: solid 1px #ffd400;
    }

    .comment-body .comment-actions {
        visibility: hidden
    }

    .comment-body:hover .comment-actions {
        visibility: visible
    }
</style>