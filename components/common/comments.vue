<template>
    <div v-observe-visibility="onVisChange">
        <flex column gap="2">
            <flex>
                <h3>Comments</h3>
                <div class="ml-auto my-auto">
                    <a-button icon="comment" @click="setCommentDialog(true)">Comment</a-button>
                </div>
            </flex>
            <flex column gap="2" v-if="comments.data.length > 0">
                <a-comment v-for="comment of comments.data" 
                    :key="comment.id"
                    :data="comment"
                    :can-edit-all="canEditAll"
                    :current-focus="replyingComment || editingComment"
                    @delete="deleteComment"
                    @reply="replyToComment"
                    @pin="setCommentPinState"
                    @edit="beginEditingComment"
                />
            </flex>
            <va-inner-loading v-else-if="!isLoaded" class="mb-4" :loading="true"/>
            <div v-else class="text-center">
                <h4>No Comments</h4>
            </div>
        </flex>
        <transition name="fade">
            <div v-if="showCommentDialog" class="fixed bottom-0 left-0 right-0 p-3">
                <flex column class="mx-auto w-7/12" gap="2">
                    <h3 v-if="replyingComment">Replying Comment</h3>
                    <h3 v-else-if="editingComment">Editing Comment</h3>
                    <h3 v-else>Commenting</h3>
                    <md-editor v-model="commentContent" rows="12"/>
                    <flex class="text-right">
                        <a-button @click="commentDialogConfirm" icon="comment">{{$t('comment')}}</a-button>
                        <a-button @click="setCommentDialog(false)" icon="times">{{$t('close')}}</a-button>
                    </flex>
                </flex>
            </div>
        </transition>
    </div>
</template>

<script setup>
    const props = defineProps({
        url: String,
        canEditAll: Boolean
    });

    const isLoaded = ref(false);
    const comments = ref({ data: [] });
    const commentContent = ref('');
    const showCommentDialog = ref(false);
    const replyingComment = ref();
    const editingComment = ref();

    function setCommentDialog(open) {
        showCommentDialog.value = open;
        commentContent.value = '';
        replyingComment.value = undefined;
        editingComment.value = undefined;
    }

    async function postComment() {
        const content = commentContent.value;
        try {
            commentContent.value = '';
            const comment = await usePost(props.url, {
                content,
                reply_to: replyingComment.value?.id
            });
            if (replyingComment.value) {
                replyingComment.value.last_replies.push(comment);
            } else {
                comments.value.data.unshift(comment);
            }
            setCommentDialog(false);
        } catch (error) {
            commentContent.value = content; //We failed, let's not eat the user's draft
            // Notification.error('Failed to post the comment');
            console.log(error);
        }                
    }

    async function editComment() {
        const content = commentContent.value;
        try {
            commentContent.value = '';
            await usePatch(props.url + '/' + editingComment.value.id, { content });
            editingComment.value.content = content;
            setCommentDialog(false);
        } catch (error) {
            commentContent.value = content; //We failed, let's not eat the user's draft
            // Notification.error('Failed to edit the comment');
            console.log(error);
        }
    }

    function commentDialogConfirm() {
        if (editingComment.value) {
            editComment();
        } else {
            postComment();
        }
    }

    async function loadComments() {
        const data = await useGet(props.url);
        comments.value = data;
        isLoaded.value = true;
    }

    function onVisChange(isVisible) {
        if (!isLoaded.value && isVisible) {
            loadComments();
        }
    }

    async function deleteComment(commentId, isReply=false) {
        await useDelete(props.url + '/' + commentId);
        if (!isReply) {
            const allComments = comments.value.data;
            allComments.splice(allComments.findIndex(com => com.id == commentId));
        }
    }
    
    function replyToComment(replyTo, mention) {
        setCommentDialog(true);
        
        if (replyTo) {
            replyingComment.value = replyTo;
            if (mention) {
                replyingComment.value = replyTo;
                commentContent.value = `@${mention} `;
            }
        }
    }

    //This really just reloads the comments(will later reset pages)
    //Pretty much because this isn't as frequent and so it's sorted well.
    async function setCommentPinState(comment) {
        await usePatch(props.url + '/' + comment.id, { pinned: comment.pinned });
        loadComments();
    }

    function beginEditingComment(comment) {
        setCommentDialog(true);
        commentContent.value = comment.content;
        editingComment.value = comment;
    }
</script>

<style scoped>

</style>