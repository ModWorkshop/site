<template>
    <m-flex
        :class="['thread', 'content-block', 'max-md:gap-2', !!thread.answer_comment_id ? 'thread-inactive' : undefined]"
        gap="1"
        column
    >
        <m-flex class="items-center" gap="2" wrap>
            <m-flex column class="md:flex-1">
                <NuxtLink class="max-md:text-lg md:text-lg" :to="`/thread/${thread.id}`">
                    <i-ri-checkbox-circle-fill v-if="!!thread.answer_comment_id" class="text-success"/>
                    <i-ri-checkbox-circle-line v-if="thread.closed || thread.closed_by_mod" class="text-secondary"/>
                    {{thread.name}}
                </NuxtLink>

                <m-flex wrap class="items-center text-sm">
                    <i18n-t :keypath="(noCategory && (thread.category || !forumId)) ? 'user_posted' : 'user_posted_in_category'">
                        <template #user>
                            <a-user :user="thread.user" :avatar="false"/>
                        </template>
                        <template #timeAgo>
                            <m-time :datetime="thread.created_at" relative/>
                        </template>
                        <template #place>
                            <m-flex class="items-center text-inherit">
                                <NuxtLink v-if="!forumId" :to="thread.game_id ? `/g/${thread.game?.short_name}/forum` : '/forum'">
                                    {{ thread.game_id ? (thread.game?.name ?? $t('not_available')) : $t('global_forum') }}
                                </NuxtLink>
                                <i-mdi-menu-right v-if="!forumId"/>
                                <NuxtLink :to="categoryLink ? `${to}?category=${thread.category_id}` : undefined">
                                    {{thread.category?.emoji}} {{thread.category?.name ?? $t('not_available')}}
                                </NuxtLink>
                            </m-flex>
                        </template>
                    </i18n-t>
                </m-flex>

                <m-flex gap="2">
                    <m-flex v-if="thread.tags?.length">
                        <NuxtLink v-for="tag in thread.tags" :key="tag.id" :to="`${to}?selected-tags=${tag.id}`">
                            <m-tag :color="tag.color" small>{{tag.name}}</m-tag>
                        </NuxtLink>
                    </m-flex>
                </m-flex>
            </m-flex>


            <m-flex column class="ml-auto">
                <m-flex class="flex-1">
                    <m-flex class="ml-auto items-center" gap="2">
                        <div><i-mdi-message-reply/> {{ thread.comment_count }}</div>
                        <i-mdi-pin v-if="!noPins && thread.pinned_at" style="transform: rotate(45deg);"/>
                        <i-mdi-lock v-if="thread.locked"/>
                    </m-flex>
                </m-flex>
                <m-flex class="md:items-center max-md:flex-col max-md:gap-4">
                    <m-flex v-if="thread.comment_count" class="md:ml-auto items-center" gap="1" wrap>
                        <a-user v-if="thread.last_user" :user="thread.last_user" avatar-size="xs"/>
                        <m-time :datetime="thread.bumped_at" relative/>
                    </m-flex>
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
    padding: 1rem;
    color: var(--secondary-text-color);
}

.thread-inactive {
    background-color: var(--secondary-content-bg-color);
}
</style>