<template>
    <flex style="flex: 1;" gap="3">
        <content-block style="flex: 4;">
            <a-table v-if="threads" :background="false">
                <template #head>
                    <th>{{$t('title')}}</th>
                    <th>{{$t('poster')}}</th>
                    <th v-if="!categoryName">{{$t('category')}}</th>
                    <th>{{$t('last_activity')}}</th>
                    <th>{{$t('last_reply')}}</th>
                </template>
                <tr v-for="thread in threads.data" :key="thread.created_at" class="cursor-pointer" @click="clickThread(thread)">
                    <td>
                        <font-awesome-icon v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2" icon="thumbtack"/>
                        <NuxtLink :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
                    </td>
                    <td><a-user :user="thread.user"/></td>
                    <td v-if="!categoryName">
                        <a v-if="thread.category">{{thread.category.name}}</a>
                        <span v-else>-</span>
                    </td>
                    <td><time-ago :time="thread.bumped_at"/></td>
                    <td v-if="thread.last_user"><a-user :user="thread.last_user"/></td>
                    <td v-else>None</td>
                </tr>
            </a-table>
        </content-block>
        <content-block style="flex: 1;">
            <a-input v-model="query" label="Search"/>
            <a-select v-if="!forumId" v-model="selectedForum" :label="$t('forum')" placeholder="Any forum" clearable :options="forums"/>
            <a-select v-model="selectedTags" label="Tags" placeholder="Select Tags" multiple clearable :options="tags.data" max-selections="10"/>
            <flex v-if="currentForumId" column gap="2">
                <label>{{$t('category')}}</label>
                <template v-if="categories.data.length">
                    <a class="nav-link" @click="categoryName = undefined">
                        All
                    </a>
                    <a v-for="category of categories.data" :key="category.name" class="nav-link cursor-pointer" @click="categoryName = category.name">
                        {{category.name}}
                    </a>
                </template>
                <span v-else>
                    No categories found!
                </span>
            </flex>
        </content-block>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ForumCategory, Game, Tag, Thread } from '~~/types/models';

const router = useRouter();
const query = useRouteQuery('query', '');
const categoryName = useRouteQuery('category');
const selectedForum = useRouteQuery('forum');
const selectedTags = useRouteQuery('selected-tags', []);
const { t } = useI18n();

const props = defineProps<{
    gameId?: number,
    forumId?: number,
    noPins?: boolean
}>();

const currentForumId = computed(() => selectedForum.value ?? props.forumId);
const currentGameId = computed(() => props.gameId);

function clickThread(thread: Thread) {
    router.push(`/thread/${thread.id}`);
}

const { data: categories, refresh: refreshCats } = await useFetchMany<ForumCategory>('forum-categories', {
    params: reactive({ forum_id: currentForumId }),
    immediate: !!currentForumId.value
});

const params = reactive({
    forum_id: currentForumId,
    tags: selectedTags,
    category_name: categoryName,
    query: query,
    no_pins: props.noPins ? 1 : 0
});

const { data: threads, refresh } = await useFetchMany<Thread>('threads', { params });

const { data: tags, refresh: refreshTags } = await useFetchMany<Tag>('tags', { 
    params: reactive({ 
        game_id: currentGameId,
        type: 'forum',
        global: 1
    })
});

const { data: games } = await useFetchMany<Game>('games');

const forums = computed(() => {
    const forums = [{ id: 1, name: t('global_forum') }];

    for (const game of games.value.data) {
        forums.push({ name: game.name, id: game.forum_id });
    }

    return forums;
});

const { start } = useTimeoutFn(refresh, 200);
watch(params, start);

watch(selectedForum, async () => {
    await refreshCats();
    await refreshTags();
});
</script>