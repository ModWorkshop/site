<template>
    <div v-intersection-observer="onVisChange">
        <flex column gap="3">
            <flex class="items-center">
                <span class="h3">{{$t(resourceName)}}</span>
                <flex class="ml-auto">
                    <a-button v-if="viewingComment" :to="pageUrl">
                        <i-mdi-arrow-left/> {{$t(`return_to_${resourceName}`)}}
                    </a-button>
                    <VTooltip v-else :disabled="canComment">
                        <a-button :disabled="!canComment" @click="onClickComment">
                            <i-mdi-comment/> {{$t('post')}}
                        </a-button>
                        <template #popper>{{cannotCommentReason}}</template>
                    </VTooltip>
                    <a-button @click="subscribe">
                        <i-mdi-bell-off v-if="commentable.subscribed"/>
                        <i-mdi-bell v-else/>
                        {{$t(commentable.subscribed ? 'unsubscribe' : 'subscribe')}}
                    </a-button>
                </flex>
            </flex>
            <a-pagination v-if="comments && !viewingComment" v-model="page" :total="comments.meta.total" :per-page="comments.meta.per_page"/>
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
                    :commentable="commentable"
                    fetch-replies
                    @delete="deleteComment"
                    @reply="replyToComment"
                    @pin="setCommentPinState"
                    @edit="beginEditingComment"
                />
                <template v-else-if="comments">
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
                        :commentable="commentable"
                        @delete="deleteComment"
                        @reply="replyToComment"
                        @pin="setCommentPinState"
                        @edit="beginEditingComment"
                    />
                </template>
            </flex>
            <a-loading v-else-if="!isLoaded"/>
            <h4 v-else class="text-center">{{$t(`no_${resourceName}_found`)}}</h4>
        </flex>
        <VDropdown v-model:shown="showMentions" class="fixed" strategy="fixed" placement="right-end" :style="{left: `${mentionPos[0]}px`, top: `${mentionPos[1]+16}px`}" auto-size="min" no-auto-focus>
            <template #popper>
                <flex v-if="users" column class="p-3" style="max-width: 300px; word-break: normal; overflow: hidden;">
                    <template v-if="users.data.length">
                        <a-user v-for="u in users.data" :key="u.id" :user="u" avatar static show-at class="cursor-pointer whitespace-pre" @click="e => onClickMention(e, u)"/>
                    </template>
                    <div v-else>{{ $t('no_users_found') }}</div>
                </flex>
            </template>
        </VDropdown>
        <transition>
            <div v-if="showCommentDialog" class="floating-editor">
                <flex column class="mx-auto page-block-xs float-bg" gap="0">
                    <h3 v-if="replyingComment">{{$t('replying')}}</h3>
                    <h3 v-else-if="editingComment">{{$t('editing')}}</h3>
                    <md-editor v-model="commentContent" class="mt-2" rows="12" minlength="2" maxlength="5000" required @keyup="onTextareaKeyup" @mousedown="onTextareaMouseDown" @input="onTextareaInput"/>
                    <flex class="text-right p-2">
                        <a-button :disabled="!posting && commentContent.length < 2" @click="submit">
                            <i-mdi-comment/> {{$t('submit')}}
                        </a-button>
                        <a-button @click="setCommentDialog(false)">
                            <i-mdi-close-thick/> {{$t('close')}}
                        </a-button>
                    </flex>
                </flex>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { Comment, Game, User } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { vIntersectionObserver } from '@vueuse/components';
import { useStore } from '~~/store';
import { remove } from '@vue/shared';
import getCaretCoordinates from 'textarea-caret';

const props = withDefaults(defineProps<{
    lazy?: boolean,
    url: string,
    pageUrl: string,
    resourceName?: string,
    commentable: { id: number, subscribed?: boolean, game?: Game },
    canComment: boolean,
    getSpecialTag: (comment: Comment) => string|undefined,
    canEditAll: boolean,
    cannotCommentReason?: string,
    canDeleteAll: boolean
}>(), { resourceName: 'comments', lazy: false });

const { user } = useStore();
const router = useRouter();
const route = useRoute();
const focusComment = useRouteQuery('comment');

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

const showError = useQuickErrorToast();

const { data: comments, refresh: loadComments } = await useFetchMany<Comment>(props.url, {
    immediate: (!props.lazy || !!focusComment?.value) && !route.params.comment,
    params: reactive({
        page,
        limit: 20
    })
});

watch(comments, () => isLoaded.value = !!comments.value, { immediate: true });
watch(page, loadComments);

const { data: viewingComment } = await useFetchData<Comment>(`comments/${route.params.comment}`, {
    immediate: !!route.params.comment
});

async function subscribe() {
    try {
        if (props.commentable.subscribed) {
            await deleteRequest(`${props.url}/subscription`);
        } else {
            await postRequest(`${props.url}/subscription`);
        }
        props.commentable.subscribed = !props.commentable.subscribed;
    } catch (error) {
        showError(error);
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
    const coords = getCaretCoordinates(textArea, textArea.selectionEnd);
    const rect = textArea.getBoundingClientRect();

    mentionPos.value = [coords.left + rect.left, coords.top + rect.top];

    if (event.key == 'Enter' || event.key == 'ArrowUp' || event.key == 'Ctrl' || event.key == 'ArrowDown') {
        showMentions.value = false;
    }
}

function onTextareaInput(event: InputEvent) {
    const textArea = event.target as HTMLTextAreaElement;

    if (event.inputType == 'insertText' || event.inputType == 'insertFromPaste') {
        if (event.inputType == 'insertText' && event.data == '@') {
            mentionRange.value = [textArea.selectionEnd, textArea.selectionEnd];
            showMentions.value = true;
        } else if (showMentions.value) {
            mentionRange.value = [mentionRange.value[0], textArea.selectionEnd];
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
watch([commentContent, mentionRange], async () => {
    if (lastTimeout) {
        clearTimeout(lastTimeout);
    }
    if (showMentions.value) {
        const query = commentContent.value.substring(mentionRange.value[0], mentionRange.value[1]);
        
        lastTimeout = setTimeout(async () => {
            users.value = undefined;
            users.value = await useGetMany<User>('users', { params: { query } });
            for (const user of users.value.data) {
                usersCache[user.unique_name] = user;
            }
        }, 150);
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
    const mentions: string[] = [];
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
        const comment = await postRequest<Comment>(props.url, {
            content: processedContent,
            mentions,
            reply_to: replyingComment.value?.id
        });
        if (replyingComment.value && replyingComment.value.replies) {
            replyingComment.value.replies ??= [];
            replyingComment.value.replies.push(comment);
        } else if (comments.value) {
            comments.value.data.unshift(comment);
        }
        posting.value = false;
        commentContent.value = '';
        setCommentDialog(false);
    } catch (error) {
        posting.value = false;
        showError(error);
        console.log(error);
    }
}

async function editComment() {
    const content = commentContent.value;
    try {
        commentContent.value = '';
        const { mentions, content: processedContent } = processMentions(content);

        await patchRequest(`comments/${editingComment.value!.id}`, { content: processedContent, mentions });
        editingComment.value!.content = content;
        setCommentDialog(false);
    } catch (error) {
        commentContent.value = content; //We failed, let's not eat the user's draft
        // Notification.error('Failed to edit the comment');
        showError(error);
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
function onVisChange(entries: IntersectionObserverEntry[]) {
    if (!isLoaded.value && entries[0].isIntersecting) {
        loadComments();
    }
}

async function deleteComment(comment: Comment, isReply=false) {
    try {
        await deleteRequest(`comments/${comment.id}`);
        if (!isReply) {
            remove(comments.value!.data, comment);
        }
    } catch (error) {
        showError(error);
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
    try {
        await patchRequest(`comments/${comment.id}`, { pinned: comment.pinned });
        loadComments();
    } catch (error) {
        showError(error);
    }
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
    z-index: 1000;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
}

.floating-editor > div {
    border-radius: var(--border-radius);
    padding: 1rem;
}
</style>