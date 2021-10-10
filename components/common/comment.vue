<template>
    <flex column :class="{comment: true, reply: isReply, focus: isFocused, 'mb-2': true, 'bg-dark': true}" :id="`comment-cid${comment.id}`">
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
                    <font-awesome-icon v-if="comment.pinned" icon="pin" :title="$t('pinned')"/>
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
                    <a v-if="canReply" class="reply-button text-body mr-1 cursor-pointer" title="Reply" role="button"><font-awesome-icon class="reply"/></a>
                    <a v-if="!isReply" class="subscribe text-body mr-1 cursor-pointer" :title="comment.subbed ? $t('unsubscribe') : $t('subscribe')" role="button">
                        <font-awesome-icon :icon="comment.subbed ? 'slash' : 'bell'"/>
                    </a>
                    <el-dropdown trigger="click" placement="bottom" @visible-change="setActionsVisible">
                        <a class="cursor-pointer text-body">
                            <font-awesome-icon icon="ellipsis-h"/>
                        </a>
                        <el-dropdown-menu>
                            <dropdown-item v-if="canEdit">{{$t('edit')}}</dropdown-item>
                            <dropdown-item v-if="!isReply && canEditAll" @click="togglePinnedState">{{comment.pinned ? $t('unpin') : $t('pin')}}</dropdown-item>
                            <dropdown-item v-if="canEdit">{{$t('delete')}}</dropdown-item>
                            <dropdown-item>{{$t('report')}}</dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </flex>
            </div>
        </flex>
        <template v-if="!isReply">
            <div class="replies">
                
            </div>
            <div v-if="comment.total_replies > 3" class="mx-4 mb-3">
                <a class="load-more-replies cursor-pointer">{{$t('load_more')}} ({{comment.total_replies}} {{$t('replies')}})</a>
            </div>
        </template>
    </flex>
</template>

<script>
import { timeAgo } from '../../utils/helpers';
import { mapState } from 'vuex';
import { ref } from '@vue/composition-api';

export default {
    props: {
        comment: {},
        parent: {},
        canEditAll: Boolean,
        isReply: Boolean,
    },
    computed: {
        canEdit() {
            return this.user.id === this.comment.user_id || this.canEditAll;
        },
        canReport() {
            return false; //TODO: implement report system
        },
        canReply() {
            return false; //TODO: implement reply system
        },
        isFocused() {
            return false; //TODO implement focused comment
        },
        ...mapState([
            'user'
        ])
    },
    mounted() {
        setInterval(() => {
            this.updateKey++; //I'm not sure if I wanna keep this but lol this is piss easy to implement
        }, 5000);
    },
    setup(props) {
        const areActionsVisible = ref(false);
        const updateKey = ref(0);
        
        async function togglePinnedState() {
            
        }
        
        function setActionsVisible(visible) {
            areActionsVisible.value = visible;
        }

        return { timeAgo, togglePinnedState, setActionsVisible, areActionsVisible, updateKey };
    }
};
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