<template>
    <m-flex
        :class="['thread', 'content-block', !!thread.answer_comment_id ? 'thread-inactive' : undefined]"
        column
    >
        <NuxtLink class="card-title" :to="`/thread/${thread.id}`">
            <a-thumbnail :src="thread.thumbnail" url-prefix="threads/images"/>
        </NuxtLink>
        <m-flex column class="p-4" gap="2">
            <m-flex class="items-center" wrap>
                <NuxtLink class="card-title" :to="`/thread/${thread.id}`">
                    <i-ri-checkbox-circle-fill v-if="!!thread.answer_comment_id" class="text-success"/>
                    {{thread.name}}
                </NuxtLink>
                <m-flex class="ml-auto items-center" gap="2">
                    <div><i-mdi-message-reply/> {{ thread.comment_count }}</div>
                    <i-mdi-lock v-if="thread.locked"/>
                    <i-mdi-pin v-if="!noPins && thread.pinned_at" style="transform: rotate(45deg);"/>
                </m-flex>
            </m-flex>
    
            <a-user :user="thread.user" avatar-size="xs"/>
            <m-flex class="items-center">
                <m-time-ago :time="thread.created_at"/>
            </m-flex>
  
            <m-flex v-if="!noCategory" class="items-center text-inherit">
                <NuxtLink v-if="!forumId" :to="thread.game_id ? `/g/${thread.game?.short_name}/forum` : '/forum'">
                    {{ thread.game_id ? (thread.game?.name ?? $t('not_available')) : $t('global_forum') }}
                </NuxtLink>
                <i-mdi-menu-right v-if="!forumId"/>
                <NuxtLink :to="categoryLink ? `${to}?category=${thread.category_id}` : undefined" class="text-inherit">
                    {{thread.category?.emoji}} {{thread.category?.name ?? $t('not_available')}}
                </NuxtLink>
            </m-flex>
    
            <md-content :text="thread.content" class="thread-content" remove-tags :padding="0"/>
    
            <m-flex class="flex-1">
                <m-flex v-if="thread.tags?.length">
                    <NuxtLink v-for="tag in thread.tags" :key="tag.id" :to="`${to}?selected-tags=${tag.id}`">
                        <m-tag :color="tag.color" small>{{tag.name}}</m-tag>
                    </NuxtLink>
                </m-flex>
            </m-flex>
        </m-flex>
    </m-flex>
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

const to = computed(() => thread.game ? `/g/${thread.game.short_name}/forum` : '/forum');
</script>

<style scoped>
.thread {
    font-size: 13px;
    color: var(--secondary-text-color);
}

.thread-inactive {
    background-color: var(--secondary-content-bg-color);
}

.thread-content {
    overflow-y: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    margin: 0 0.5rem;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}
</style>