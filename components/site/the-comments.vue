<template>
    <div v-intersection-observer="onVisChange">
        <flex column gap="2">
            <flex class="items-center">
                <h3>{{$t(resourceName)}}</h3>
                <flex class="ml-auto">
                    <a-button v-if="viewingComment" icon="arrow-left" :to="pageUrl">{{$t(`return_to_${resourceName}`)}}</a-button>
                    <Popper v-else :content="cannotCommentReason" hover :disabled="canComment">
                        <a-button icon="comment" :disabled="!canComment" @click="onClickComment">{{$t('post')}}</a-button>
                    </Popper>
                    <a-button :icon="commentable.subscribed ? 'bell-slash' : 'bell'" @click="subscribe">{{$t(commentable.subscribed ? 'unsubscribe' : 'subscribe')}}</a-button>
                </flex>
            </flex>
            <a-pagination v-if="comments && !viewingComment" v-model="page" :total="comments.meta.total" :per-page="comments.meta.per_page" @update="loadComments"/>
            <flex v-if="viewingComment || (comments && comments.data.length)" column gap="2">
                <a-comment v-if="viewingComment"
                    :url="url"
                    :page-url="pageUrl"
                    :comment="viewingComment"
                    :can-comment="canComment"
                    :can-edit-all="canEditAll"
                    :can-delete-all="canDeleteAll"
                    :current-focus="replyingComment || editingComment"
                    :get-special-tag="getSpecialTag"
                    fetch-replies
                    @delete="deleteComment"
                    @reply="replyToComment"
                    @pin="setCommentPinState"
                    @edit="beginEditingComment"
                />
                <template v-else>
                    <a-comment v-for="comment of comments.data" 
                        :key="comment.id"
                        :url="url"
                        :page-url="pageUrl"
                        :comment="comment"
                        :can-comment="canComment"
                        :can-edit-all="canEditAll"
                        :can-delete-all="canDeleteAll"
                        :current-focus="replyingComment || editingComment"
                        :get-special-tag="getSpecialTag"
                        @delete="deleteComment"
                        @reply="replyToComment"
                        @pin="setCommentPinState"
                        @edit="beginEditingComment"
                    />
                </template>
            </flex>
            <a-loading v-else-if="!isLoaded"/>
            <h4 v-else class="text-center">No Comments</h4>
        </flex>
        <transition>
            <div v-if="showCommentDialog" class="floating-editor">
                <flex column class="mx-auto w-8/12" gap="2">
                    <h3 v-if="replyingComment">{{$t('replying')}}</h3>
                    <h3 v-else-if="editingComment">{{$t('editing')}}</h3>
                    <md-editor v-model="commentContent" rows="12" minlength="2" required @keyup="onTextareaKeyup" @mousedown="onTextareaMouseDown" @input="onTextareaInput"/>
                    <div v-show="showMentions" class="fixed" :style="{left: `${mentionPos[0]}px`, top: `${mentionPos[1]}px`}">
                        <flex v-if="users" column class="mention-float">
                            <template v-if="users.data.length">
                                <a-user v-for="u in users.data" :key="u.id" :user="u" avatar static show-at class="cursor-pointer" @click="e => onClickMention(e, u)"/>
                            </template>
                            <div v-else>
                                No users found!
                            </div>
                        </flex>
                    </div>
                    <flex class="text-right">
                        <a-button icon="comment" :disabled="!posting && commentContent.length < 2" @click="submit">{{$t('submit')}}</a-button>
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
import { remove } from '@vue/shared';
const props = withDefaults(defineProps<{
    lazy: boolean,
    url: string,
    pageUrl: string,
    resourceName?: string,
    commentable: { subscribed?: boolean },
    canComment?: boolean,
    getSpecialTag?: (comment: Comment) => string,
    canEditAll?: boolean,
    cannotCommentReason?: string,
    canDeleteAll?: boolean
}>(), { resourceName: 'comments' });

const { $caretXY } = useNuxtApp();

const { user } = useStore();
const router = useRouter();
const route = useRoute();

const isLoaded = ref(false);
const page = useRouteQuery('page', 1);

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

const posting = ref(false);

const { showToast } = useToaster();

const { data: comments, refresh: loadComments } = await useFetchMany<Comment>(props.url, {
    immediate: !props.lazy && !route.params.commentId,
    params: reactive({
        page,
        limit: 20
    })
});

watch(comments, () => isLoaded.value = true);

const { data: viewingComment } = await useFetchData<Comment>(`${props.url}/${route.params.commentId}`, {
    immediate: !!route.params.commentId
});

async function subscribe() {
    try {
        if (props.commentable.subscribed) {
            await useDelete(`${props.url}/subscription`);
        } else {
            await usePost(`${props.url}/subscription`);
        }
        props.commentable.subscribed = !props.commentable.subscribed;
    } catch (error) {
        console.log(error);
    }
}

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

/**
 * Sets visiblity of the comments float
 */
function setCommentDialog(open: boolean) {
    showCommentDialog.value = open;
    commentContent.value = '';
    replyingComment.value = undefined;
    editingComment.value = undefined;
}

/**
 * Here we process the text and search for mentions.
 * In order to prevent loss of mentions for users, we convert the mentions to IDs we could easily replace later 
 */
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
    try {
        const { mentions, content: processedContent } = processMentions(content);
        posting.value = true;
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
        posting.value = false;
        commentContent.value = '';
        setCommentDialog(false);
    } catch (error) {
        posting.value = false;
        showToast({ desc: 'Could not post comment', color: 'danger' });
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

function submit() {
    if (editingComment.value) {
        editComment();
    } else {
        postComment();
    }
}

/**
 * Called when the comments come into view so we can load them lazily
 */
function onVisChange(isVisible: boolean) {
    if (!isLoaded.value && isVisible) {
        loadComments();
    }
}

async function deleteComment(comment: Comment, isReply=false) {
    await useDelete(props.url + '/' + comment.id);
    if (!isReply) {
        remove(comments.value.data, comment);
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

/**
 * This really just reloads the comments
 * Pretty much because this isn't as frequent and so it's sorted well.
 */
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
    background-color: var(--alt-content-bg-color);
    padding: 1rem;
    width: 250px;
    height: 180px;
    overflow-y: scroll;
}
</style>