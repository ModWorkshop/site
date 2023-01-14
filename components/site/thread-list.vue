<template>
    <flex style="flex: 1;" class="flex-col md:flex-row" gap="3">
        <content-block column class="md:self-start" style="flex: 1;">
            <a-input v-model="query" :label="$t('search')"/>
            <a-select v-if="!forumId" v-model="selectedForum" :label="$t('forum')" :placeholder="$t('any_forum')" clearable :options="forums"/>
            <a-select v-model="selectedTags" :label="$t('tags')" multiple clearable :options="tags.data" max-selections="10"/>
            <flex v-if="currentForumId" column>
                <label>{{$t('category')}}</label>
                <button-group v-if="categories.data.length" v-model:selected="categoryName" class="mt-2" column button-style="nav">
                    <a-group-button :name="null"><font-awesome-icon icon="comments"/> {{$t('all')}}</a-group-button>
                    <a-group-button v-for="category of categories.data" :key="category.name" :name="category.name">
                        {{category.emoji}} {{category.name}}
                    </a-group-button>
                </button-group>
            </flex>
        </content-block>
        <flex column style="flex: 4;">
            <a-pagination v-if="threads" v-model="page" :total="threads.meta.total" :per-page="20"/>
            <a-table v-if="threads && !loading">
                <thead>
                    <th>{{$t('title')}}</th>
                    <th>{{$t('poster')}}</th>
                    <th v-if="!categoryName">{{$t('category')}}</th>
                    <th>{{$t('last_activity')}}</th>
                    <th>{{$t('last_reply_by')}}</th>
                </thead>
                <tbody>
                    <tr v-for="thread in threads.data" :key="thread.created_at" class="cursor-pointer content-block" @click="clickThread(thread)">
                        <td>
                            <font-awesome-icon v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2" icon="thumbtack"/>
                            <NuxtLink :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
                        </td>
                        <td><a-user :user="thread.user" @click.stop/></td>
                        <td v-if="!categoryName">
                            <NuxtLink v-if="thread.category" @click.stop="categoryName = thread.category.name">{{thread.category.emoji}} {{thread.category.name}}</NuxtLink>
                            <span v-else>-</span>
                        </td>
                        <td><time-ago :time="thread.bumped_at"/></td>
                        <td v-if="thread.last_user"><a-user :user="thread.last_user" @click.stop/></td>
                        <td v-else>{{$t('none')}}</td>
                    </tr>
                </tbody>
            </a-table>
            <a-loading v-else/>
            <a-pagination v-if="threads && !loading" v-model="page" :total="threads.meta.total" :per-page="20"/>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ForumCategory, Game, Tag, Thread } from '~~/types/models';

const props = defineProps<{
    gameId?: number,
    forumId?: number,
    noPins?: boolean,
    query?: boolean,
}>();

const router = useRouter();
const query = props.query ? useRouteQuery('query', '') : ref('');
const page = useRouteQuery('page', 1, 'number');
const categoryName = useRouteQuery('category');
const selectedForum = useRouteQuery('forum');
const selectedTags = useRouteQuery('selected-tags', []);
const loading = ref(false);
const { t } = useI18n();

const emit = defineEmits<{
    (e: 'selectCategory', cat: ForumCategory): void
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
watch(currentCategroy, val => emit('selectCategory', val), { immediate: true });

const params = reactive({
    forum_id: currentForumId,
    tags: selectedTags,
    category_name: categoryName,
    query: query,
    no_pins: props.noPins ? 1 : 0,
    limit: 20,
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

    for (const game of games.value.data) {
        forums.push({ name: game.name, id: game.forum_id });
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
</script>