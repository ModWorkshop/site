<template>
    <tr :class="{'cursor-pointer': true, 'thread': true}" @click="clickThread(thread)">
        <td @click.self="clickThread(thread)">
            <m-flex column gap="3" class="py-2">
                <m-flex>
                    <i-mdi-pin v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2"/>
                    <i-material-symbols-check-circle v-if="!!thread.answer_comment_id" class="mr-2 text-success"/>
                    <NuxtLink :class="{'opacity-60': !!thread.answer_comment_id}" :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
                </m-flex>
                <m-flex class="items-center" gap="3">
                    <div v-if="thread.category">
                        <i-mdi-square-medium/>
                        <NuxtLink :to="categoryLink ? `${to}?category=${thread.category_id}` : undefined">{{thread.category.emoji}} {{thread.category.name}}</NuxtLink>
                    </div>
                    <m-flex v-if="thread.tags?.length" wrap @click.stop>
                        <NuxtLink v-for="tag in thread.tags" :key="tag.id" :to="`${to}?selected-tags=${tag.id}`">
                            <m-tag :color="tag.color">{{tag.name}}</m-tag>
                        </NuxtLink>
                    </m-flex>
                </m-flex>
            </m-flex>
        </td>
        <td v-if="!userId" @click.self="clickThread(thread)"><a-user :user="thread.user" avatar-size="xs" @click.stop/></td>
        <td v-if="!forumId">{{ thread.game_id ? (thread.game?.name ?? $t('not_available')) : $t('global_forum') }}</td>
        <td @click.self="clickThread(thread)">{{ thread.comment_count }}</td>
        <td @click.self="clickThread(thread)"><m-time-ago :time="thread.bumped_at"/></td>
        <td v-if="thread.last_user" @click.self="clickThread(thread)"><a-user :user="thread.last_user" avatar-size="xs" @click.stop/></td>
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
.thread {
    padding: 2rem;
}
</style>