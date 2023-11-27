<template>
    <m-content-block ref="contentBlockRef" :alt-background="isReply" :gap="3" :padding="3" :class="classes">
        <m-flex class="comment-body">
            <div v-if="!isReply && comment.reply_to" :title="$t('reply')" class="my-auto"><i-mdi-reply/></div>
            <NuxtLink class="mr-1 self-start" :to="`/user/${comment.user_id}`">
                <m-avatar class="align-middle" :src="comment.user?.avatar" size="md"/>
            </NuxtLink>
            <m-flex column wrap class="overflow-hidden w-full">
                <m-flex wrap>
                    <a-user :avatar="false" :user="comment.user"/>
                    <span v-if="specialTag" class="text-success">({{specialTag}})</span>
                    <NuxtLink class="ml-1 text-secondary" :to="commentPage">
                        <m-time-ago :time="comment.created_at"/>
                    </NuxtLink>
                    <span v-if="comment.updated_at != comment.created_at" class="text-secondary" :title="comment.updated_at">{{$t('edited')}}</span>
                    <span v-if="showPins && comment.pinned" :title="$t('pinned')">
                        <i-mdi-pin class="transform rotate-45"/>
                    </span>
                </m-flex>
                <md-content class="w-full comment-content" :text="content"/>
            </m-flex>
            <div v-if="url" class="absolute" style="right: -0.5rem; top: -0.5rem;">
                <m-flex class="comment-actions text-body flex-col md:flex-row" :style="{visibility: areActionsVisible ? 'visible' : null}">
                    <m-button v-if="canReply" class="cursor-pointer" :title="$t('reply')" size="sm" @click="user ? $emit('reply', comment) : $router.push('/login')">
                        <i-mdi-reply/>
                    </m-button>
                    <m-button
                        v-if="!isReply"
                        class="cursor-pointer"
                        size="sm"
                        :title="comment.subscribed ? $t('unsubscribe') : $t('subscribe')"
                        :to="!user ? '/login' : undefined"
                        @click="subscribe"
                    >
                        <i-mdi-bell-off v-if="comment.subscribed"/>
                        <i-mdi-bell v-else/>
                    </m-button>
                    <report-button v-if="user" v-model:show-modal="showReportModal" :button="false" resource-name="comment" :url="`comments/${comment.id}/reports`"/>
                    <m-dropdown v-model:open="areActionsVisible" style="margin: 0; border: 0;">
                        <m-button class="cursor-pointer" size="sm">
                            <i-mdi-dots-vertical/>
                        </m-button>
                        <template #content>
                            <m-dropdown-item v-if="canEdit" @click="$emit('edit', comment)">{{$t('edit')}}</m-dropdown-item>
                            <m-dropdown-item v-if="canPin && !comment.reply_to" @click="togglePinnedState">{{comment.pinned ? $t('unpin') : $t('pin')}}</m-dropdown-item>
                            <m-dropdown-item
                                v-if="canPin && comment.commentable_type == 'thread'"
                                @click="$emit('markAsAnswer', comment)"
                            >
                                {{(commentable as Thread).answer_comment_id == comment.id ? $t('unmark_as_answer') : $t('mark_as_answer')}}
                            </m-dropdown-item>
                            <m-dropdown-item v-if="canEdit || canDeleteAll" @click="openDeleteModal">{{$t('delete')}}</m-dropdown-item>
                            <m-dropdown-item :to="!user ? '/login' : undefined" @click="showReportModal = true">{{$t('report')}}</m-dropdown-item>
                        </template>
                    </m-dropdown>
                </m-flex>
            </div>
        </m-flex>
        <div v-if="replies?.data?.length" class="mx-3">
            <m-flex column>
                <list-comment v-for="reply of replies.data" 
                    :key="reply.id"
                    :url="url"
                    :page-url="pageUrl"
                    :comment="reply"
                    :can-comment="canReply"
                    :can-edit-all="canEditAll"
                    :can-delete-all="canDeleteAll"
                    :current-focus="currentFocus"
                    :get-special-tag="getSpecialTag"
                    is-reply
                    @edit="() => $emit('edit', reply)"
                    @reply="$emit('reply', comment, reply.user)"
                    @delete="deleteComment"
                />
            </m-flex>
            <m-pagination 
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
    </m-content-block>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import type { Comment, Game, Mod, User, Thread } from '~~/types/models';
import { Paginator } from '~~/types/paginator';

const props = withDefaults(defineProps<{
    comment: Comment,
    game?: Game,
    url?: string,
    pageUrl?: string,
    canComment?: boolean,
    canEditAll?: boolean,
    canDeleteAll?: boolean,
    commentable?: Thread|Mod,
    canPin?: boolean,
    isReply?: boolean,
    showPins?: boolean,
    getSpecialTag?: (comment: Comment) => string|undefined,
    currentFocus?: Comment,
    fetchReplies?: boolean
}>(), { showPins: true });

const emit = defineEmits<{
    reply: [comment: Comment, user?: User],
    delete: [comment: Comment, isReply: boolean],
    pin: [comment: Comment],
    edit: [comment: Comment],
    markAsAnswer: [comment: Comment]
}>();

const page = useRouteQuery('page', 1, null, true);
const store = useStore();
const { user } = store;
const YesNoModal = useYesNoModal();
const { t } = useI18n();

const focusComment = useRouteQuery('comment');
const { params } = useRoute();
const content = toRef(props.comment, 'content');
const showReportModal = ref(false);
const contentBlockRef = ref();
const areActionsVisible = ref(false);

const specialTag = computed(() => props.getSpecialTag && props.getSpecialTag(props.comment));

const { data: fetchedReplies, refresh: loadReplies } = useFetchMany<Comment>(props.fetchReplies ? `comments/${props.comment.id}/replies` : '', {
    immediate: props.fetchReplies,
    lazy: true,
    params: reactive({ page, limit: 20 })
});
const replies = computed(() => props.fetchReplies ? fetchedReplies.value : new Paginator<Comment>(props.comment.last_replies));

const isAnswer = computed(() => {
    return props.commentable && Object.hasOwn(props.commentable, 'answer_comment_id') 
        && (props.commentable as Thread)?.answer_comment_id == props.comment.id;
});

watch(replies, val => {
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

const canEdit = computed(() => user?.id === props.comment.user_id || store.hasPermission('manage-discussions', props.game) || props.canEditAll);
const canReply = computed(() => props.canComment && !props.comment.user?.blocked_me);

const classes = computed(() => ({
    comment: true,
    reply: props.isReply,
    relative: true,
    focus: focusComment.value == props.comment.id || (props.currentFocus && props.currentFocus.id == props.comment.id),
    answer: isAnswer.value
}));

const commentPage = computed(() => {
    if (props.comment.reply_to) {
        return `/${props.comment.commentable_type}/${props.comment.commentable_id}/post/${props.comment.reply_to}?comment=${props.comment.id}`;
    } else {
        return `/${props.comment.commentable_type}/${props.comment.commentable_id}/post/${props.comment.id}`;
    }
});

onMounted(() => {
    if ((focusComment.value == props.comment?.id) || params.comment) {
        const element: HTMLDivElement = contentBlockRef.value.element;
        
        if (element) {
            setTimeout(() => {
                element.scrollIntoView({ block: 'nearest' });
                window.scrollBy(0, 16);
            }, 150);
        }
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

<style scoped>
.answer {
    background-color: #73ff001a !important;
    border-left: solid 3px #09ff00;
    border-radius: var(--border-radius);
}
</style>

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
    visibility: hidden;
}

.comment-body:hover .comment-actions {
    visibility: visible;
}
</style>