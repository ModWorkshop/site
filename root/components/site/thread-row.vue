<template>
    <tr :class="{'cursor-pointer': true, 'thread': true}" @click="clickThread(thread)">
        <td @click.self="clickThread(thread)">
            <m-flex>
                <i-mdi-pin v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2"/>
                <i-material-symbols-check-circle v-if="!!thread.answer_comment_id" class="mr-2 text-success"/>
                <NuxtLink :class="{'thread-row': truncate, 'opacity-60': !!thread.answer_comment_id}" :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
            </m-flex>
        </td>
        <td v-if="!userId" @click.self="clickThread(thread)"><a-user :user="thread.user" @click.stop/></td>
        <td v-if="!forumId">{{ thread.game_id ? (thread.game?.name ?? $t('not_available')) : $t('global_forum') }}</td>
        <td v-if="!noCategory" @click.stop>
            <NuxtLink v-if="thread.category" :to="categoryLink ? `${to}?category=${thread.category_id}` : undefined">{{thread.category.emoji}} {{thread.category.name}}</NuxtLink>
            <span v-else>-</span>
        </td>
        <td>
            <m-flex v-if="thread.tags?.length" wrap @click.stop>
                <NuxtLink v-for="tag in thread.tags" :key="tag.id" :to="`${to}?selected-tags=${tag.id}`">
                    <m-tag :color="tag.color" >{{tag.name}}</m-tag>
                </NuxtLink>
            </m-flex>
            <span v-else>-</span>
        </td>
        <td @click.self="clickThread(thread)">{{ thread.comment_count }}</td>
        <td @click.self="clickThread(thread)"><m-time-ago :time="thread.bumped_at"/></td>
        <td v-if="thread.last_user" @click.self="clickThread(thread)"><a-user :user="thread.last_user" @click.stop/></td>
        <td v-else @click.self="clickThread(thread)">{{$t('none')}}</td>
    </tr>
</template>

<script setup lang="ts">
import type { Thread } from '~/types/models';

const { thread, categoryLink } = defineProps<{
    thread: Thread,
    userId?: number,
    noPins?: boolean,
    forumId?: number,
    categoryLink?: boolean,
    noCategory?: boolean
}>();

const router = useRouter();
const to = computed(() => thread.game ? `/g/${thread.game.short_name}/forum` : '/forum');

function clickThread(thread: Thread) {
    router.push(`/thread/${thread.id}`);
}
</script>

<style scoped>
.thread-row {
    white-space: pre-line;
    min-width: 100px;
}
</style>