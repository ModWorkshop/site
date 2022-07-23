<template>
    <div v-observe-visibility="onVisChange">
        <flex column gap="2">
            <flex>
                <h3>Comments</h3>
                <div class="ml-auto my-auto">
                    <a-button icon="comment" @click="setCommentDialog(true)">Comment</a-button>
                </div>
            </flex>
            <a-pagination v-if="comments" v-model="page" v-model:pages="pages" :total="comments.meta.total" :per-page="comments.meta.per_page" @update="loadComments"/>
            <flex v-if="comments && comments.data.length > 0" column gap="2">
                <a-comment v-for="comment of comments.data" 
                    :key="comment.id"
                    :data="comment"
                    :can-edit-all="canEditAll"
                    :current-focus="replyingComment || editingComment"
                    :get-special-tag="getSpecialTag"
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
                    <md-editor v-model="commentContent" rows="12" @keyup="onTextareaKeyup" @mousedown="onTextareaMouseDown" @input="onTextareaInput"/>
                    <div v-show="showMentions" class="fixed" :style="{left: `${mentionPos[0]}px`, top: `${mentionPos[1]}px`}">
                        <flex v-if="users" column class="mention-float">
                            <template v-if="users.data.length">
                                <a-user v-for="user in users.data" :key="user.id" :user="user" avatar no-links show-at class="cursor-pointer" @click="e => onClickMention(e, user)"/>
                            </template>
                            <div v-else>
                                No users found!
                            </div>
                        </flex>
                    </div>
                    <flex class="text-right">
                        <a-button icon="comment" @click="commentDialogConfirm">{{$t('comment')}}</a-button>
                        <a-button icon="times" @click="setCommentDialog(false)">{{$t('close')}}</a-button>
                    </flex>
                </flex>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
    import { Comment, User } from '~~/types/models';
    import { Paginator } from '~~/types/paginator';

    const props = defineProps({
        url: { type: String, required: true },
        getSpecialTag: Function,
        canEditAll: Boolean
    });

    const { $caretXY } = useNuxtApp();

    const isLoaded = ref(false);
    const comments = ref<Paginator<Comment>>();
    const page = ref(1);
    const pages = ref(1);
    const commentContent = ref('');
    const showCommentDialog = ref(false);
    const replyingComment = ref<Comment>();
    const editingComment = ref<Comment>();

    //Mention stuff
    const mentionPos = ref([0, 0]);
    const showMentions = ref(false);
    const mentionRange = ref([-1,-1]);
    const users = ref<Paginator<User>>();

    function onTextareaKeyup(event: KeyboardEvent) {
        if (!(event.target instanceof HTMLTextAreaElement)) {
            console.log(event.target);
            return;
        }

        const textArea = event.target as HTMLTextAreaElement;
        const coords = $caretXY(textArea);
        
        mentionPos.value = [coords.left, coords.top];

        if (event.key == 'Enter' || event.key == 'ArrowUp' || event.key == 'Ctrl' || event.key == 'ArrowDown') {
            showMentions.value = false;
        }
    }

    function onTextareaInput(event: InputEvent) {
        const textArea = event.target as HTMLTextAreaElement;

        if (event.inputType == 'insertText') {
            if (event.data == '@') {
                mentionRange.value = [textArea.selectionEnd, textArea.selectionEnd];
                showMentions.value = true;
                console.log('at');
            } else if (showMentions.value) {
                mentionRange.value[1] = textArea.selectionEnd;
            }
        }
    }

    function onTextareaMouseDown(event: MouseEvent) {
        if (event.button == 0) {
            showMentions.value = false;
        }
    }

    function onClickMention(e: MouseEvent, user: User) {
        showMentions.value = false;
        const range = mentionRange.value;

        commentContent.value = strReplacRange(commentContent.value, range[0]-1, range[1], `@${user.unique_name}`);
    }

    let lastTimeout: NodeJS.Timeout;
    watch(commentContent, async (val: string) => {
        if (lastTimeout) {
            clearTimeout(lastTimeout);
        }
        if (showMentions.value) {
            const query = val.substring(mentionRange.value[0], mentionRange.value[1]);
            
            lastTimeout = setTimeout(async () => {
                users.value = null;
                users.value = await useGetMany<User>('users', { params: { query } });
            }, 500);
        }

    });

    function setCommentDialog(open: boolean) {
        showCommentDialog.value = open;
        commentContent.value = '';
        replyingComment.value = undefined;
        editingComment.value = undefined;
    }

    async function postComment() {
        const content = commentContent.value;
        try {
            commentContent.value = '';
            const comment = await usePost<Comment>(props.url, {
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
        comments.value = await useGetMany<Comment>(props.url, {
            params: {
                page: page.value,
                limit: 20,
            }
        });
        isLoaded.value = true;
    }

    function onVisChange(isVisible: boolean) {
        if (!isLoaded.value && isVisible) {
            loadComments();
        }
    }

    async function deleteComment(commentId: number, isReply=false) {
        await useDelete(props.url + '/' + commentId);
        if (!isReply) {
            const allComments = comments.value.data;
            allComments.splice(allComments.findIndex(com => com.id == commentId));
        }
    }
    
    function replyToComment(replyTo: Comment, mention: string) {
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
    async function setCommentPinState(comment: Comment) {
        await usePatch(props.url + '/' + comment.id, { pinned: comment.pinned });
        loadComments();
    }

    function beginEditingComment(comment: Comment) {
        setCommentDialog(true);
        commentContent.value = comment.content;
        editingComment.value = comment;
    }
</script>

<style>
.mention-float {
    background-color: #22262a;
    padding: 1rem;
    width: 250px;
    height: 180px;
    overflow-y: scroll;
}
</style>