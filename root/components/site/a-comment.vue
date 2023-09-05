<template>
    <content-block ref="contentBlockRef" :alt-background="isReply" :gap="3" :padding="3" :class="classes">
        <flex class="comment-body">
            <NuxtLink class="mr-1 self-start" :to="`/user/${comment.user_id}`">
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
                    <span :title="$t('pinned')">
                        <i-mdi-pin v-if="comment.pinned" class="transform rotate-45"/>
                    </span>
                </flex>
                <a-markdown class="w-full comment-content" :text="content"/>
            </flex>
            <div class="absolute" style="right: -0.5rem; top: -0.5rem;">
                <flex class="comment-actions text-body flex-col md:flex-row" :style="{visibility: areActionsVisible ? 'visible' : null}">
                    <a-button v-if="canReply" class="cursor-pointer" :title="$t('reply')" size="sm" @click="user ? $emit('reply', comment) : $router.push('/login')">
                        <i-mdi-reply/>
                    </a-button>
                    <a-button
                        v-if="!isReply"
                        class="cursor-pointer"
                        size="sm"
                        :title="comment.subscribed ? $t('unsubscribe') : $t('subscribe')"
                        @click="subscribe"
                    >
                        <i-mdi-bell-off v-if="comment.subscribed"/>
                        <i-mdi-bell v-else/>
                    </a-button>
                    <a-report v-if="user" v-model:show-modal="showReportModal" :button="false" resource-name="comment" :url="`comments/${comment.id}/reports`"/>
                    <VDropdown v-model:shown="areActionsVisible" style="margin: 0; border: 0;">
                        <a-button class="cursor-pointer" size="sm">
                            <i-mdi-dots-vertical/>
                        </a-button>
                        <template #popper>
                            <a-dropdown-item v-if="canEdit" @click="$emit('edit', comment)">{{$t('edit')}}</a-dropdown-item>
                            <a-dropdown-item v-if="!isReply && (canPin || canEditAll)" @click="togglePinnedState">{{comment.pinned ? $t('unpin') : $t('pin')}}</a-dropdown-item>
                            <a-dropdown-item v-if="canEdit || canDeleteAll" @click="openDeleteModal">{{$t('delete')}}</a-dropdown-item>
                            <a-dropdown-item :to="!user ? '/login' : undefined" @click="showReportModal = true">{{$t('report')}}</a-dropdown-item>
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
            <NuxtLink v-else-if="!fetchReplies && comment.replies_count && comment.replies_count > 3" class="my-2" :to="commentPage">
                {{$t('read_all_replies')}} ({{$t('replies_n', comment.replies_count)}})
            </NuxtLink>
        </div>
    </content-block>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Comment, Game } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
const YesNoModal = useYesNoModal();

const props = defineProps<{
    comment: Comment,
    url: string,
    pageUrl: string,
    canComment: boolean,
    canEditAll: boolean,
    canDeleteAll: boolean,
    canPin?: boolean,
    isReply?: boolean,
    commentable: { id: number, game?: Game },
    getSpecialTag: (comment: Comment) => string|undefined,
    currentFocus?: Comment,
    fetchReplies?: boolean
}>();

const { t } = useI18n();
const specialTag = computed(() => props.getSpecialTag && props.getSpecialTag(props.comment));
const focusComment = useRouteQuery('comment');
const { params } = useRoute();

const content = toRef(props.comment, 'content');

const showReportModal = ref(false);

const contentBlockRef = ref();

onMounted(() => {
    if ((focusComment.value == props.comment?.id) || params.comment) {
        const element: HTMLDivElement = contentBlockRef.value.element;
        console.log(element);
        
        if (element) {
            setTimeout(() => {
                element.scrollIntoView({ block: 'nearest' });
                window.scrollBy(0, 16);
            }, 150);
        }
    }
});

const page = ref(1);
const { data: fetchedReplies, refresh: loadReplies } = useFetchMany<Comment>(props.fetchReplies ? `comments/${props.comment.id}/replies` : '', {
    immediate: props.fetchReplies,
    lazy: true,
    params: reactive({ page, limit: 20 })
});

const replies = computed(() => props.fetchReplies ? fetchedReplies.value : new Paginator<Comment>(props.comment.last_replies));

watch(replies, (val: Paginator<Comment>) => {
    props.comment.replies = val?.data ?? [];
}, { immediate: true });

if (props.comment.mentions) {
    content.value = content.value.replace(/<@([0-9]+)>/g, (match, id) => {
        const user = props.comment.mentions.find(user => user.id == id);
    
        if (user) {
            return `@${user.unique_name}`;
        } else {
            return `<\\@${id}>`;
        }
    });
}

const emit = defineEmits([
    'reply',
    'delete',
    'pin',
    'edit'
]);

const { user, hasPermission } = useStore();

const areActionsVisible = ref(false);
const canEdit = computed(() => user && (hasPermission('create-discussions', props.commentable.game) && user.id === props.comment.user_id) || props.canEditAll);
const canReply = computed(() => props.canComment && !props.comment.user?.blocked_me);

const classes = computed(() => ({
    comment: true,
    reply: props.isReply,
    relative: true,
    focus: focusComment.value == props.comment.id || (props.currentFocus && props.currentFocus.id == props.comment.id)
}));

const commentPage = computed(() => {
    if (props.comment.reply_to) {
        return `${props.pageUrl}/post/${props.comment.reply_to}?comment=${props.comment.id}`;
    } else {
        return `${props.pageUrl}/post/${props.comment.id}`;
    }
});

async function subscribe() {
    try {
        const url = `comments/${props.comment.id}`;
        if (props.comment.subscribed) {
            await deleteRequest(`${url}/subscription`);
        } else {
            await postRequest(`${url}/subscription`);
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
    remove(replies.value!.data, comment);
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

.comment-content {
    max-height: 500px;
    overflow: auto;
}

.reply {
    background-color: var(--alt-content-bg-color);
}

.comment-body .comment-actions {
    visibility: hidden
}

.comment-body:hover .comment-actions {
    visibility: visible
}
</style>