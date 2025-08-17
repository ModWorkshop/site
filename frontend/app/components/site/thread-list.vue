<template>
    <m-flex v-intersection-observer="onVisChange" column gap="3">
        <span v-if="title" class="h2">
            <NuxtLink class="text-body" :to="titleLink">{{title}}</NuxtLink>
        </span>
        <m-flex style="flex: 1;" class="flex-col md:flex-row" gap="3">
            <m-flex v-if="filters" class="max-md:!w-full items-center" style="width: 300px;" column gap="3">
                <m-content-block column class="w-full">
                    <m-input v-model="query" :label="$t('search')"/>
                    <m-select v-if="!forumId" v-model="selectedForum" :label="$t('forum')" :placeholder="$t('any_forum')" clearable :options="forums"/>
                    <m-select 
                        v-if="tags"
                        v-model="selectedTags"
                        color-by="color"
                        list-tags
                        :label="$t('tags')"
                        multiple clearable
                        :options="tags.data"
                        max-selections="10"
                    />
                    <m-flex v-if="currentForumId && categories?.data.length" column>
                        <label>{{$t('category')}}</label>
                        <m-toggle-group v-model:selected="categoryId" class="mt-2" column button-style="nav">
                            <m-toggle-group-item :value="undefined"><i-mdi-comment/> {{$t('all')}}</m-toggle-group-item>
                            <template v-for="category of categories.data" :key="category.id">
                                <m-toggle-group-item v-if="!category.hidden" :value="category.id">
                                    {{category.emoji}} {{category.name}}
                                </m-toggle-group-item>
                            </template>
                        </m-toggle-group>
                    </m-flex>
                </m-content-block>
                <div id="mws-ads-filters"/>
            </m-flex>
            <m-flex column gap="3" style="flex: 4;">
                <m-alert v-if="currentCategory && currentCategory.desc" color="info">
                    {{ currentCategory.desc}}
                </m-alert>
                <m-pagination v-if="filters && threads" v-model="page" :total="threads.meta.total" :per-page="20"/>
                
                <m-toggle-group v-if="currentCategory?.can_close_threads" v-model:selected="displayClosed" gap="1" button-style="nav">
                    <m-toggle-group-item :value="false">{{$t('open_threads')}}</m-toggle-group-item>
                    <m-toggle-group-item :value="true">{{$t('closed_threads')}}</m-toggle-group-item>
                </m-toggle-group>

                <template v-if="!loading && threads?.data.length">
                    <m-flex v-if="currentCategory?.grid_mode" gap="2" class="threads-grid" column>
                        <grid-thread 
                            v-for="thread in threads.data"
                            :key="thread.created_at"
                            :thread="thread"
                            category-link
                            :no-category="!!categoryId"
                            :no-pins="noPins"
                            :forum-id="currentForumId"
                            :user-id="userId"
                        />
                    </m-flex>
                    <m-flex v-else gap="2" class="threads" column>
                        <list-thread
                            v-for="thread in threads.data"
                            :key="thread.created_at"
                            :thread="thread"
                            category-link
                            :no-category="!!categoryId"
                            :no-pins="noPins"
                            :forum-id="currentForumId"
                            :user-id="userId"
                        />
                    </m-flex>
                </template>
                <m-loading v-else-if="loading || !loaded" class="m-auto"/>
                <h3 v-else class="mx-auto">
                    {{$t('no_threads_found')}}
                </h3>
                <m-pagination v-if="filters && threads && !loading" v-model="page" :total="threads.meta.total" :per-page="20"/>
            </m-flex>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { ForumCategory, Game, Tag, Thread } from '~/types/models';
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
    lazy?: boolean,
    url?: string,
    userId?: number,
}>(), {
    lazy: false,
    query: true,
    filters: true,
    limit: 20,
});

const query = props.query ? useRouteQuery('query', '') : ref('');

if (props.filters) {
    useInsertAd('mws-ads-filters', {
        sizes: [[ "300", "250" ]],
        renderVisibleOnly: true,
        report: {
            position: "bottom-left"
        }
    });
}

if (query) {
    searchBus.on(search => query.value = search);
}

const displayClosed = props.query ? useRouteQuery('closed', false, 'boolean') : ref(false);
const page = props.query ? useRouteQuery('page', 1, 'number') : ref(1);
const categoryId = props.query ? useRouteQuery('category', null, 'number') : ref();
const selectedForum = props.query ? useRouteQuery('forum', null, 'number') : ref();
const selectedTags = props.query ?  useRouteQuery('selected-tags', []) : ref([]);
const loading = ref(false);
const loaded = ref(!props.lazy);

const { t } = useI18n();

const emit = defineEmits<{
    (e: 'selectCategory', cat?: ForumCategory): void
}>();

const currentForumId = computed(() => selectedForum.value ?? props.forumId);
const currentGameId = computed(() => props.gameId);
const currentUrl = computed(() => props.url ?? (props.userId ? `/users/${props.userId}/threads` : '/threads'));

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
    closed: displayClosed,
    no_pins: props.noPins ? 1 : 0,
    limit: props.limit,
    page
});

watch(() => currentCategory.value?.can_close_threads, canClose => {
    if (canClose) {
        displayClosed.value = false;
    } else {
        displayClosed.value = undefined;
    }
})

const { data: threads, refresh } = await useFetchMany<Thread>(currentUrl.value, { immediate: !props.lazy, params });

async function onVisChange(entries: IntersectionObserverEntry[]) {
    if (entries[0].isIntersecting) {
        await refresh();
        loaded.value = true;
    }
}

const { data: tags, refresh: refreshTags } = await useFetchMany<Tag>(currentGameId.value ? `games/${currentGameId.value}/tags` : 'tags', {
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

<style scoped>
.threads {
    word-break: break-word;
}
</style>