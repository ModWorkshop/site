<template>
    <content-block :alt-background="isReply" :gap="null" :class="{comment: true, reply: isReply, focus: isFocused}" :id="`comment-cid${comment.id}`">
        <flex class="comment-body mx-4">
            <div class="mr-2" style="margin-top: 1rem;">
                <nuxt-link :to="`/user/${comment.user_id}`">
                    <a-avatar :src="`http://localhost:8000/storage/${comment.user.avatar}`" size="medium"/>
                </nuxt-link>
            </div>
            <flex column wrap class="overflow-hidden w-full mt-3">
                <div :key="updateKey">
                    <a-user :avatar="false" :user="comment.user"/>
                    <span v-if="comment.special_type" class="text-success">({{comment.special_type}})</span>
                    <a class="text-body" :title="comment.created_at" :href="`/post/${comment.id}`">{{timeAgo(comment.created_at)}}</a>
                    <span v-if="comment.updated_at != comment.created_at" :title="comment.updated_at">{{$t('edited')}}</span>
                    <font-awesome-icon v-if="comment.pinned" class="transform rotate-45" icon="thumbtack" :title="$t('pinned')"/>
                </div>
                <form v-if="comment.canedit" method="post" class="hidden_form p-2 d-none" style="width:100%;">
                    <!-- <textarea class="edited_message" name="edited_message" v-model="comment.content"/> -->
                    <input type="submit" :value="$t('edit')" class="confirm_edit btn btn-primary mt-2"> 
                    <button class="stop_editing btn btn-primary mt-2">{{$t('cancel')}}</button>
                </form>
                <div class="comment_message p-1 text-break markdown w-full">
                    <markdown :text="comment.content"/>
                </div>
            </flex>
            <div class="float-right mt-3">
                <flex class="comment-actions" :style="{visibility: areActionsVisible ? 'visible' : null}">
                    <a v-if="canReply" class="reply-button text-body mr-1 cursor-pointer" title="Reply" role="button" @click="$emit('reply', comment)">
                        <font-awesome-icon icon="reply"/>
                    </a>
                    <a v-if="!isReply" class="subscribe text-body mr-1 cursor-pointer" :title="comment.subbed ? $t('unsubscribe') : $t('subscribe')" role="button">
                        <font-awesome-icon :icon="comment.subbed ? 'slash' : 'bell'"/>
                    </a>
                    <el-dropdown trigger="click" placement="bottom" @visible-change="setActionsVisible">
                        <a class="cursor-pointer text-body">
                            <font-awesome-icon icon="ellipsis-h"/>
                        </a>
                        <el-dropdown-menu>
                            <dropdown-item v-if="canEdit" @click="$emit('edit', comment)">{{$t('edit')}}</dropdown-item>
                            <dropdown-item v-if="!isReply && canEditAll" @click="togglePinnedState">{{comment.pinned ? $t('unpin') : $t('pin')}}</dropdown-item>
                            <dropdown-item v-if="canEdit" @click="openDeleteModal">{{$t('delete')}}</dropdown-item>
                            <dropdown-item>{{$t('report')}}</dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </flex>
            </div>
        </flex>
        <template v-if="!isReply">
            <flex column gap="1" class="replies px-6">
                <a-comment v-for="reply of comment.last_replies" 
                    :key="reply.id"
                    :data="reply"
                    :can-edit-all="canEditAll"
                    is-reply
                    @reply="$emit('reply', comment, reply.user.name)"
                    @delete="deleteComment"
                />
            </flex>
            <div v-if="comment.total_replies > 3" class="mx-4 mb-3">
                <a class="load-more-replies cursor-pointer">{{$t('load_more')}} ({{comment.total_replies}} {{$t('replies')}})</a>
            </div>
        </template>
    </content-block>
</template>

<script setup>
import { timeAgo } from '../../utils/helpers';
import { MessageBox } from 'element-ui';
import { useStore } from '@nuxtjs/composition-api';

const props = defineProps({
    data: Object,
    parent: Object,
    canEditAll: Boolean,
    isReply: Boolean
});

const comment = computed(() => props.data);

defineEmits([
    'reply',
    'delete',
    'pin',
    'edit'
]);

const store = useStore();

const areActionsVisible = ref(false);
const updateKey = ref(0);

const canEdit = computed(() => store.state.user.id === comment.value.user_id || props.canEditAll);
// const canReport = computed(() => false);
const canReply = computed(() => true);
const isFocused = computed(() => false);

onMounted(() => {
    setInterval(() => { //TODO: don't do this for things that were posted long ago
        updateKey.value++; //I'm not sure if I wanna keep this but lol this is piss easy to implement
    }, 5000);
});

async function togglePinnedState() {
    comment.value.pinned = !comment.value.pinned;
    this.$emit('pin', comment.value);
}

function setActionsVisible(visible) {
    areActionsVisible.value = visible;
}

//Deletes a reply in the comment
function deleteComment(commentId, isReply) {
    const lastReplies = comment.value.last_replies;
    this.$delete(lastReplies, lastReplies.findIndex(com => com.id === commentId));
    this.$emit('delete', commentId, isReply);
}

function openDeleteModal() {
    MessageBox.confirm('This will delete the comment', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning'
    }).then(() => this.$emit('delete', comment.value.id, props.isReply));
    //If it's a reply, it will call its parent comment's deleteComment function and then call the actual holder of the comments.
}
</script>

<style>
    .reply {
        background-color: #151719;
    }

    .comment-body .comment-actions {
        visibility: hidden
    }

    .comment-body:hover .comment-actions {
        visibility: visible
    }
</style>