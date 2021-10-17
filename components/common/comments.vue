<template>
    <div v-observe-visibility="onVisChange">
        <flex column gap="2">
            <flex>
                <h3>Comments</h3>
                <div class="ml-auto my-auto">
                    <a-button icon="comment" @click="setCommentDialog(true)">Comment</a-button>
                </div>
            </flex>
            <comment v-for="comment of comments.data" :key="comment.id" :comment="comment" :can-edit-all="canEditAll" @reply="setCommentDialog"/>
        </flex>
        <transition name="fade">
            <div v-if="showCommentDialog" class="fixed bottom-0 left-0 right-0 p-3">
                <flex column class="mx-auto w-7/12" gap="2">
                    <h3 v-if="replyToComment">Replying</h3>
                    <h3 v-else>Commenting</h3>
                    <md-editor v-model="commentContent" rows="12"/>
                    <div class="text-right">
                        <a-button @click="setCommentDialog(false)" icon="times">{{$t('close')}}</a-button>
                        <a-button @click="postComment" icon="comment">{{$t('comment')}}</a-button>
                    </div>
                </flex>
            </div>
        </transition>
    </div>
</template>

<script setup>
    import { Notification } from 'element-ui';

    const props = defineProps({
        url: String,
        canEditAll: Boolean
    });

    const isLoaded = ref(false);
    const comments = ref({});
    const commentContent = ref('');
    const showCommentDialog = ref(false);
    const replyToComment = ref(null);

    function setCommentDialog(open, replyTo, mention) {
        showCommentDialog.value = open;
        commentContent.value = '';
        replyToComment.value = null;

        if (open) {
            if (replyTo) {
                replyToComment.value = replyTo;
                if (mention) {
                    replyToComment.value = replyTo;
                    commentContent.value = `@${mention} `;
                }
            }
        }
    }
    
    async function postComment() {
        const content = commentContent;
        try {
            commentContent.value = '';
            const comment = await this.$axios.post(props.url, {
                content,
                reply_to: replyToComment && replyToComment.id
            }).then(res => res.data);
            if (replyToComment) {
                replyToComment.last_replies.push(comment);
            } else {
                comments.data.unshift(comment);
            }
            showCommentDialog.value = false;
        } catch (error) {
            commentContent.value = content; //We failed, let's not eat the user's draft
            Notification.error('Failed to post the comment');
            console.log(error);
        }                
    }

    async function onVisChange(isVisible) {
        if (!isLoaded.value && isVisible) {
            comments.value = await this.$axios.get(props.url).then(res => res.data);

            isLoaded.value = true;
        }
    }
</script>

<style scoped>

</style>