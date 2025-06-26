<template>
    <m-card
        :class="['thread', 'content-block', !!thread.answer_comment_id ? 'thread-inactive' : undefined]"
        :padding="6"
        :gap="3"
    >
        <template #title>
            <NuxtLink class="card-title" :to="`/thread/${thread.id}`">
                <m-flex inline class="items-center">
                    <i-mdi-pin v-if="!noPins && thread.pinned_at" class="text-secondary rotate-45"/>
                    <i-ri-checkbox-circle-fill v-if="!!thread.answer_comment_id" class="text-success"/>
                    <i-ri-checkbox-circle-line v-if="thread.closed || thread.closed_by_mod" class="text-secondary"/>
                    {{thread.name}}
                </m-flex>
            </NuxtLink>
        </template>

        <a-user :user="thread.user" avatar-size="xs"/>

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

        <m-flex v-if="thread.tags?.length" class="mt-auto" wrap>
            <NuxtLink v-for="tag in thread.tags" :key="tag.id" :to="`${to}?selected-tags=${tag.id}`">
                <m-tag :color="tag.color" small>{{tag.name}}</m-tag>
            </NuxtLink>
        </m-flex>

        <m-flex class="items-center mt-auto">
            <m-time :datetime="thread.created_at" relative class="mr-auto"/>
            <i-mdi-lock v-if="thread.locked"/>
            <div><i-mdi-message-reply/> {{ thread.comment_count }}</div>
        </m-flex>
    </m-card>
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
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
}
</style>