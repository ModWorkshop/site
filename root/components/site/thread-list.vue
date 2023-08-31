<template>
    <flex v-intersection-observer="onVisChange" column>
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
                    <button-group v-if="categories?.data.length" v-model:selected="categoryId" class="mt-2" column button-style="nav">
                        <a-group-button :name="undefined"><i-mdi-comment/> {{$t('all')}}</a-group-button>
                        <a-group-button v-for="category of categories.data" :key="category.id" :name="category.id">
                            {{category.emoji}} {{category.name}}
                        </a-group-button>
                    </button-group>
                </flex>
            </content-block>
            <flex column gap="3" style="flex: 4;">
                <a-alert v-if="currentCategory && currentCategory.desc" color="info">
                    {{ currentCategory.desc}}
                </a-alert>
                <a-pagination v-if="filters && threads" v-model="page" :total="threads.meta.total" :per-page="20"/>
                <a-table v-if="threads?.data.length && !loading" class="threads">
                    <template #head>
                        <th>{{$t('title')}}</th>
                        <th>{{$t('poster')}}</th>
                        <th v-if="!categoryId">{{$t('category')}}</th>
                        <th>{{$t('replies')}}</th>
                        <th>{{$t('last_activity')}}</th>
                        <th>{{$t('last_reply_by')}}</th>
                    </template>
                    <template v-if="threads.data.length" #body>
                        <a-list-thread 
                            v-for="thread in threads.data"
                            :key="thread.created_at"
                            :thread="thread"
                            category-link
                            :no-category="!!categoryId"
                            :no-pins="noPins"
                            :forum="selectedForum"
                        />
                    </template>
                </a-table>
                <a-loading v-else-if="loading || !loaded" class="m-auto"/>
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
import { vIntersectionObserver } from '@vueuse/components';

const searchBus = useEventBus<string>('search');

const props = withDefaults(defineProps<{
    title?: string,
    titleLink?: string,
    gameId?: number,
    forumId?: number,
    noPins?: boolean,
    query?: boolean,
    filters?: boolean,
    limit?: number,
    lazy?: boolean
}>(), {
    lazy: false,
    query: true,
    filters: true,
    limit: 20
});

const query = props.query ? useRouteQuery('query', '') : ref('');

if (query) {
    searchBus.on(search => query.value = search);
}

const page = props.query ? useRouteQuery('page', 1, 'number') : ref(1);
const categoryId = props.query ? useRouteQuery('category') : ref();
const selectedForum = props.query ? useRouteQuery('forum') : ref();
const selectedTags = props.query ?  useRouteQuery('selected-tags', []) : ref([]);
const loading = ref(false);
const loaded = ref(!props.lazy);

const { t } = useI18n();

const emit = defineEmits<{
    (e: 'selectCategory', cat?: ForumCategory): void
}>();

const currentForumId = computed(() => selectedForum.value ?? props.forumId);
const currentGameId = computed(() => props.gameId);

const { data: categories, refresh: refreshCats } = await useFetchMany<ForumCategory>('forum-categories', {
    params: reactive({ forum_id: currentForumId }),
    immediate: !!currentForumId.value && props.filters
});

const currentCategory = computed(() => categories.value?.data.find(cat => cat.id == categoryId.value));
watch(categoryId, () => {
    emit('selectCategory', currentCategory.value);
    page.value = 1;
}, { immediate: true });

const params = reactive({
    forum_id: currentForumId,
    tags: selectedTags,
    category_id: categoryId,
    query: query,
    no_pins: props.noPins ? 1 : 0,
    limit: props.limit,
    page
});

const { data: threads, refresh } = await useFetchMany<Thread>('threads', { immediate: !props.lazy, params });

async function onVisChange(entries: IntersectionObserverEntry[]) {
    if (entries[0].isIntersecting) {
        await refresh();
        loaded.value = true;
    }
}

const { data: tags, refresh: refreshTags } = await useFetchMany<Tag>(currentGameId.value ? `games/${currentGameId}/tags` : 'tags', {
    immediate: props.filters,
    params: reactive({ 
        type: 'forum',
        global: 1
    })
});

const { data: games } = await useFetchMany<Game>('games', { immediate: !currentGameId.value && props.filters });

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
}, 200, { immediate: false });
watch(params, () => {
    if (props.filters) {
        loading.value = true;
        start();
    }
});

watch(selectedForum, async () => {
    await refreshCats();
    await refreshTags();
});
</script>