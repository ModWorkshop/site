<template>
    <flex column>
        <h2 v-if="title">
            <NuxtLink class="text-body" :to="titleLink">{{title}}</NuxtLink>
        </h2>
        <flex column style="flex: 4;">
            <flex v-for="thread in threads.data" :key="thread.created_at" column class="cursor-pointer content-block p-2" @click="clickThread(thread)">
                <flex>
                    <font-awesome-icon v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" icon="thumbtack"/>
                    <NuxtLink :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
                </flex>
                <flex class="items-center">
                    <a-user :user="thread.user" avatar-size="xs" @click.stop/>
                    <time-ago :time="thread.bumped_at"/>
                </flex>
            </flex>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { Thread } from '~~/types/models';

const props = defineProps<{
    title?: string,
    titleLink?: string,
    gameId?: number,
    forumId?: number,
    noPins?: boolean,
    query?: boolean,
}>();

const router = useRouter();
const query = props.query ? useRouteQuery('query', '') : ref('');
const selectedForum = useRouteQuery('forum');
const selectedTags = useRouteQuery('selected-tags', []);

const currentForumId = computed(() => selectedForum.value ?? props.forumId);

function clickThread(thread: Thread) {
    router.push(`/thread/${thread.id}`);
}

const params = reactive({
    forum_id: currentForumId,
    tags: selectedTags,
    query: query,
    no_pins: props.noPins ? 1 : 0
});

const { data: threads, refresh } = await useFetchMany<Thread>('threads', { params });

const { start } = useTimeoutFn(refresh, 200);
watch(params, start);
</script>