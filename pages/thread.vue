<template>
    <page-block v-if="thread">
        <the-breadcrumb :items="breadcrumb"/>
        <flex>
            <a-button v-if="canEdit" :to="`${thread.id}/edit`" icon="cog">{{$t('edit')}}</a-button>
            <a-button color="danger">{{$t('report')}}</a-button>
            <a-button>{{$t('subscribe')}}</a-button>
        </flex>
        <div class="text-3xl">{{thread.name}}</div>
        <content-block :column="false" :gap="2" :padding="4">
            <NuxtLink class="mr-1" :to="`/user/${thread.user_id}`">
                <a-avatar class="align-middle" :src="thread.user.avatar" size="lg"/>
            </NuxtLink>
            <flex column wrap class="overflow-hidden w-full">
                <flex>
                    <a-user :avatar="false" :user="thread.user"/>
                    <time-ago :time="thread.created_at"/>
                    <span v-if="thread.updated_at != thread.created_at" class="text-secondary" :title="thread.updated_at">{{$t('edited')}}</span>
                </flex>
                <a-markdown class="mt-1" :text="thread.content"/>
            </flex>
        </content-block>
        <the-comments 
            :url="`threads/${thread.id}/comments`" 
            :can-edit-all="canEditComments"
            :can-delete-all="canDeleteComments"
            :get-special-tag="commentSpecialTag"
            :can-comment="!thread.archived"
        />
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Breadcrumb, Thread } from '~~/types/models';

const canEditComments = ref(false);
const canDeleteComments = ref(false);
const commentSpecialTag = ref(null);

const { user } = useStore();

const { data: thread } = await useResource<Thread>('thread', 'threads');

const canEdit = computed(() => thread.value.user_id === user.id);

const breadcrumb = computed(() => {
    const forum = thread.value.forum;
    const game = forum.game;

    const crumb: Breadcrumb[] = [
        { name: 'forum', attachToPrev: 'forum' },
        { name: thread.value.name },
    ];

    if (game) {
        crumb.unshift({ name: game.name, id: game.short_name, type: 'game' })
    }

    return crumb;
});

</script>

<style scoped>

</style>