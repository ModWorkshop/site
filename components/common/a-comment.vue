<template>
    <content-block :id="`comment-cid${comment.id}`" :alt-background="isReply" :gap="null" :class="{comment: true, reply: isReply, focus: currentFocus && currentFocus.id == comment.id}">
        <flex class="comment-body p-1">
            <div class="mr-1">
                <nuxt-link :to="`/user/${comment.user_id}`">
                    <a-avatar :src="comment.user.avatar" size="medium"/>
                </nuxt-link>
            </div>
            <flex column wrap class="overflow-hidden w-full mt-2">
                <flex :key="updateKey">
                    <a-user :avatar="false" :user="comment.user"/>
                    <span v-if="specialTag" class="text-success">{{specialTag}}</span>
                    <a class="ml-1 text-secondary" :title="comment.created_at" :href="`/post/${comment.id}`">{{timeAgo(comment.created_at)}}</a>
                    <span v-if="comment.updated_at != comment.created_at" class="text-secondary" :title="comment.updated_at">{{$t('edited')}}</span>
                    <font-awesome-icon v-if="comment.pinned" class="transform rotate-45" icon="thumbtack" :title="$t('pinned')"/>
                </flex>
                <div class="comment-message my-2 text-break markdown w-full">
                    <markdown :text="comment.content"/>
                </div>
            </flex>
            <div class="float-right">
                <flex class="comment-actions" :style="{visibility: areActionsVisible ? 'visible' : null}">
                    <a v-if="canReply" class="reply-button text-body mr-1 cursor-pointer" title="Reply" role="button" @click="$emit('reply', comment)">
                        <font-awesome-icon icon="reply"/>
                    </a>
                    <a v-if="!isReply" class="subscribe text-body mr-1 cursor-pointer" :title="comment.subbed ? $t('unsubscribe') : $t('subscribe')" role="button">
                        <font-awesome-icon :icon="comment.subbed ? 'slash' : 'bell'"/>
                    </a>
                    <Popper arrow @open:popper="setActionsVisible(true)" @close:popper="setActionsVisible(false)">
                        <a class="cursor-pointer text-body">
                            <font-awesome-icon icon="ellipsis-h"/>
                        </a>
                        <template #content>
                            <a-dropdown-item v-if="canEdit" @click="$emit('edit', comment)">{{$t('edit')}}</a-dropdown-item>
                            <a-dropdown-item v-if="!isReply && canEditAll" @click="togglePinnedState">{{comment.pinned ? $t('unpin') : $t('pin')}}</a-dropdown-item>
                            <a-dropdown-item v-if="canEdit" @click="openDeleteModal">{{$t('delete')}}</a-dropdown-item>
                            <a-dropdown-item>{{$t('report')}}</a-dropdown-item>
                        </template>
                    </Popper>
                </flex>
            </div>
        </flex>
        <template v-if="!isReply">
            <flex column class="replies px-6">
                <a-comment v-for="reply of comment.last_replies" 
                    :key="reply.id"
                    :data="reply"
                    :can-edit-all="canEditAll"
                    :current-focus="currentFocus"
                    :get-special-tag="getSpecialTag"
                    is-reply
                    @edit="() => $emit('edit', reply)"
                    @reply="$emit('reply', comment, reply.user.unique_name)"
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
import { timeAgo } from '~~/utils/helpers';
import { useStore } from '~~/store';
const { init: openModal } = useModal();

const props = defineProps({
    data: Object,
    parent: Object,
    canEditAll: Boolean,
    isReply: Boolean,
    getSpecialTag: Function,
    currentFocus: Object
});

const comment = computed(() => props.data);

const specialTag = computed(() => props.getSpecialTag && props.getSpecialTag(comment.value));

const emit = defineEmits([
    'reply',
    'delete',
    'pin',
    'edit'
]);

const { user } = useStore();

const areActionsVisible = ref(false);
const updateKey = ref(0);

const canEdit = computed(() => user.id === comment.value.user_id || props.canEditAll);
// const canReport = computed(() => false);
const canReply = computed(() => true);

onMounted(() => {
    setInterval(() => { //TODO: don't do this for things that were posted long ago
        updateKey.value++; //I'm not sure if I wanna keep this but lol this is piss easy to implement
    }, 5000);
});

async function togglePinnedState() {
    comment.value.pinned = !comment.value.pinned;
    emit('pin', comment.value);
}

function setActionsVisible(visible) {
    areActionsVisible.value = visible;
}

//Deletes a reply in the comment
function deleteComment(commentId: number, isReply: boolean) {
    const lastReplies = comment.value.last_replies;
    lastReplies.splice(lastReplies.findIndex(com => com.id === commentId));
    emit('delete', commentId, isReply);
}

function openDeleteModal() {
    openModal({
        message: 'This will delete the comment',
        onOk() {
            emit('delete', comment.value.id, props.isReply);
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
        background-color: #151719;
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