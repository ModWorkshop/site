<template>
    <flex column>
        <h2 v-if="title">
            <NuxtLink class="text-body" :to="titleLink">{{title}}</NuxtLink>
        </h2>
        <flex style="flex: 1;" class="flex-col md:flex-row" gap="3">
            <content-block v-if="filters" column class="md:self-start" style="flex: 1;">
                <a-input v-model="query" :label="$t('search')"/>
                <a-select v-if="!forumId" v-model="selectedForum" :label="$t('forum')" :placeholder="$t('any_forum')" clearable :options="forums"/>
                <a-select v-if="tags" v-model="selectedTags" :label="$t('tags')" multiple clearable :options="tags.data" max-selections="10"/>
                <flex v-if="currentForumId" column>
                    <label>{{$t('category')}}</label>
                    <button-group v-if="categories?.data.length" v-model:selected="categoryName" class="mt-2" column button-style="nav">
                        <a-group-button :name="undefined"><a-icon icon="comments"/> {{$t('all')}}</a-group-button>
                        <a-group-button v-for="category of categories.data" :key="category.name" :name="category.name">
                            {{category.emoji}} {{category.name}}
                        </a-group-button>
                    </button-group>
                </flex>
            </content-block>
            <flex column style="flex: 4;">
                <a-pagination v-if="filters && threads" v-model="page" :total="threads.meta.total" :per-page="20"/>
                <a-table v-if="threads?.data.length && !loading" class="threads">
                    <template #head>
                        <th>{{$t('title')}}</th>
                        <th>{{$t('poster')}}</th>
                        <th v-if="!categoryName">{{$t('category')}}</th>
                        <th>{{$t('last_activity')}}</th>
                        <th>{{$t('last_reply_by')}}</th>
                    </template>
                    <template v-if="threads.data.length" #body>
                        <tr v-for="thread in threads.data" :key="thread.created_at" class="cursor-pointer content-block thread" @click="clickThread(thread)">
                            <td>
                                <a-icon v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2" icon="thumbtack"/>
                                <NuxtLink :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
                            </td>
                            <td><a-user :user="thread.user" @click.stop/></td>
                            <td v-if="!categoryName">
                                <NuxtLink v-if="thread.category" @click.stop="onCatClicked">{{thread.category.emoji}} {{thread.category.name}}</NuxtLink>
                                <span v-else>-</span>
                            </td>
                            <td><time-ago :time="thread.bumped_at"/></td>
                            <td v-if="thread.last_user"><a-user :user="thread.last_user" @click.stop/></td>
                            <td v-else>{{$t('none')}}</td>
                        </tr>
                    </template>
                </a-table>
                <a-loading v-else-if="loading" class="m-auto"/>
                <h2 v-else class="m-auto">
                    {{$t('no_threads_found')}}
                </h2>
                <a-pagination v-if="filters && threads && !loading" v-model="page" :total="threads.meta.total" :per-page="20"/>
            </flex>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ForumCategory, Game, Tag, Thread } from '~~/types/models';

const props = withDefaults(defineProps<{
    title?: string,
    titleLink?: string,
    gameId?: number,
    forumId?: number,
    noPins?: boolean,
    query?: boolean,
    filters?: boolean,
    limit?: number
}>(), {
    query: true,
    filters: true,
    limit: 20
});

const router = useRouter();
const query = props.query ? useRouteQuery('query', '') : ref('');
const page = props.query ? useRouteQuery('page', 1, 'number') : ref(1);
const categoryName = props.query ? useRouteQuery('category') : ref();
const selectedForum = props.query ? useRouteQuery('forum') : ref();
const selectedTags = props.query ?  useRouteQuery('selected-tags', []) : ref([]);
const loading = ref(false);
const { t } = useI18n();

const emit = defineEmits<{
    (e: 'selectCategory', cat?: ForumCategory): void
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

const currentCategroy = computed(() => categories.value?.data.find(cat => cat.name === categoryName.value));
watch(currentCategroy, val => {
    emit('selectCategory', val);
    page.value = 1;
}, { immediate: true });

const params = reactive({
    forum_id: currentForumId,
    tags: selectedTags,
    category_name: categoryName,
    query: query,
    no_pins: props.noPins ? 1 : 0,
    limit: props.limit,
    page
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

    if (games.value) {
        for (const game of games.value.data) {
            forums.push({ name: game.name, id: game.forum_id });
        }
    }

    return forums;
});

const { start } = useTimeoutFn(async () => {
    await refresh();
    loading.value = false;
}, 200);
watch(params, () => {
    loading.value = true;
    start();
});

watch(selectedForum, async () => {
    await refreshCats();
    await refreshTags();
});

function onCatClicked(thread: Thread) {
    if (thread.category) {
        categoryName.value = thread.category.name;
    }
}
</script>