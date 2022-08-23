<template>
    <div v-intersection-observer="onVisChange">
        <flex column gap="2">
            <flex>
                <h3 class="my-auto">Comments</h3>
                <div class="ml-auto my-auto">
                    <va-popover stick-to-edges :message="$t('comments_disabled')" :readonly="canComment">
                        <a-button icon="comment" :disabled="!canComment" @click="onClickComment">Comment</a-button>
                    </va-popover>
                </div>
            </flex>
            <a-pagination v-if="comments" v-model="page" v-model:pages="pages" :total="comments.meta.total" :per-page="comments.meta.per_page" @update="loadComments"/>
            <flex v-if="comments && comments.data.length > 0" column gap="2">
                <a-comment v-for="comment of comments.data" 
                    :key="comment.id"
                    :comment="comment"
                    :can-edit-all="canEditAll"
                    :can-delete-all="canDeleteAll"
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
        <transition>
            <div v-if="showCommentDialog" class="floating-editor">
                <flex column class="mx-auto w-8/12" gap="2">
                    <h3 v-if="replyingComment">Replying Comment</h3>
                    <h3 v-else-if="editingComment">Editing Comment</h3>
                    <h3 v-else>Commenting</h3>
                    <md-editor v-model="commentContent" rows="12" minlength="2" required @keyup="onTextareaKeyup" @mousedown="onTextareaMouseDown" @input="onTextareaInput"/>
                    <div v-show="showMentions" class="fixed" :style="{left: `${mentionPos[0]}px`, top: `${mentionPos[1]}px`}">
                        <flex v-if="users" column class="mention-float">
                            <template v-if="users.data.length">
                                <a-user v-for="user in users.data" :key="user.id" :user="user" avatar static show-at class="cursor-pointer" @click="e => onClickMention(e, user)"/>
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
import { vIntersectionObserver } from '@vueuse/components';
import { useStore } from '~~/store';
const props = defineProps({
    url: { type: String, required: true },
    canComment: Boolean,
    getSpecialTag: Function,
    canEditAll: Boolean,
    canDeleteAll: Boolean
});

const { $caretXY } = useNuxtApp();

const { user } = useStore();
const router = useRouter();

const isLoaded = ref(false);
const comments = ref<Paginator<Comment>>();
const page = useRouteQuery('page', 1);
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
const usersCache: Record<string, User> = {};

function onClickComment() {
    if (user) {
        setCommentDialog(true);
    } else {
        router.push('/login');
    }
} 

function onTextareaKeyup(event: KeyboardEvent) {
    if (!(event.target instanceof HTMLTextAreaElement)) {
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
        } else if (showMentions.value) {
            mentionRange.value[1] = textArea.selectionEnd;
        }
    } else if (event.inputType == 'deleteContentBackward' && commentContent.value.charAt(textArea.selectionEnd) == '@') {
        showMentions.value = false;
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
            for (const user of users.value.data) {
                usersCache[user.unique_name] = user;
            }
        }, 200);
    }
});

function setCommentDialog(open: boolean) {
    showCommentDialog.value = open;
    commentContent.value = '';
    replyingComment.value = undefined;
    editingComment.value = undefined;
}

//In order to prevent loss of mentions for users, we convert the mentions to IDs we could easily replace later
function processMentions(content: string) {
    const mentions = [];
    content = content.replace(/@([a-zA-Z-_0-9]+)/g, (match, uniqueName, offset) => {
        //Don't allow more than 10 people to be mentioned
        if (mentions.length < 10 && content.charAt(offset-1) !== '\\') { //Avoid selecting escaped at's
            if (!mentions.find(name => name === uniqueName)) {
                mentions.push(uniqueName);
            }
            const user = usersCache[uniqueName];
            //TODO: search for user if they weren't cached yet.
            if (user) {
                return `<@${user.id}>`;
            }
        }

        return match;
    });

    return { mentions, content };
}

async function postComment() {
    let content = commentContent.value;
    commentContent.value = '';
    try {
        const { mentions, content: processedContent } = processMentions(content);

        const comment = await usePost<Comment>(props.url, {
            content: processedContent,
            mentions,
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
        const { mentions, content: processedContent } = processMentions(content);

        await usePatch(props.url + '/' + editingComment.value.id, { content: processedContent, mentions });
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
        allComments.splice(allComments.findIndex(com => com.id == commentId), 1);
    }
}

function replyToComment(replyTo: Comment, mention: User) {
    setCommentDialog(true);
    
    if (replyTo) {
        replyingComment.value = replyTo;
        if (mention) {
            replyingComment.value = replyTo;
            commentContent.value = `@${mention.unique_name} `;
            usersCache[mention.id] = mention;
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
.floating-editor {
    max-height: 100%;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;    
}

.floating-editor > div {
    border-radius: var(--border-radius);
    padding: 1.5rem;
    background-color: rgba(0, 0, 0, 0.25);
}

.mention-float {
    background-color: #22262a;
    padding: 1rem;
    width: 250px;
    height: 180px;
    overflow-y: scroll;
}
</style>