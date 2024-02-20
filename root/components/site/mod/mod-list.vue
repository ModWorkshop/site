<template>
    <m-flex column gap="3">
        <span v-if="title" class="h2">
            <NuxtLink class="text-body" :to="titleLink">{{title}}</NuxtLink>
        </span>
        <slot name="buttons"/>
        <m-flex class="max-md:flex-col">
            <m-flex class="overflow-auto">
                <m-flex class="flex-shrink-0">
                    <m-button :disabled="sortBy == 'bumped_at'" @click="setSortBy('bumped_at')">
                        <i-mdi-clock/> {{$t('last_updated')}}
                    </m-button>
                    <m-button :disabled="sortBy == 'published_at'" @click="setSortBy('published_at')">
                        <i-mdi-upload/> {{$t('published_at')}}
                    </m-button>
                    <m-dropdown>
                        <m-button><i-mdi-star/> {{$t('popularity')}}</m-button>
                        <template #content>
                            <m-toggle-group v-model:selected="sortBy" column>
                                <m-toggle-group-item name="score"><i-mdi-calendar-month/> {{$t('popular_monthly')}}</m-toggle-group-item>
                                <m-toggle-group-item name="weekly_score"><i-mdi-calendar-week/> {{$t('popular_weekly')}}</m-toggle-group-item>
                                <m-toggle-group-item name="daily_score"><i-mdi-calendar/> {{$t('popular_today')}}</m-toggle-group-item>
                            </m-toggle-group>
                        </template>
                    </m-dropdown>
                    <m-dropdown>
                        <m-button :title="$t('sort_by')">
                            <i-mdi-dots-vertical/>
                        </m-button>
                        <template #content>
                            <m-flex column>
                                <m-toggle-group v-model:selected="sortBy" column>
                                    <m-toggle-group-item name="best_match"><i-mdi-magnify/> {{$t('best_match')}} </m-toggle-group-item>
                                    <m-toggle-group-item name="random" @click="sortBy == 'random' && refresh()"><i-mdi-dice/> {{$t('random')}}</m-toggle-group-item>
                                    <m-toggle-group-item name="likes"><i-mdi-heart/> {{$t('likes')}}</m-toggle-group-item>
                                    <m-toggle-group-item name="downloads"><i-mdi-download/> {{$t('downloads')}}</m-toggle-group-item>
                                    <m-toggle-group-item name="views"><i-mdi-eye/>{{$t('views')}}</m-toggle-group-item>
                                    <m-toggle-group-item name="name"><i-mdi-pencil/> {{$t('name')}}</m-toggle-group-item>
                                </m-toggle-group>
                            </m-flex>
                        </template>
                    </m-dropdown>
                    <m-flex v-if="!sideFilters">
                        <m-dropdown>
                            <m-button><i-mdi-filter/></m-button>
                            <template #content>
                                <Suspense>
                                    <m-flex class="p-4" gap="3" column style="width: 300px;">
                                        <mod-filters :categories="categories" :refresh-categories="refetchCats" :refresh="refresh" :filters="searchParams" :game="game"/>
                                    </m-flex>
                                </Suspense>
                            </template>
                        </m-dropdown>
                    </m-flex>
                </m-flex>
            </m-flex>
            
            <m-flex class="md:ml-auto" gap="2">
                <m-pagination v-if="fetchedMods" v-model="page" v-model:pages="pages" :total="fetchedMods.meta.total" :per-page="fetchedMods.meta.per_page" no-hiding/>
                <m-dropdown class="max-sm:hidden">
                    <m-button :title="$t('settings')"><i-mdi-cog/></m-button>
                    <template #content>
                        <m-flex column class="p-2" gap="2">
                            <m-input :label="$t('display_mode')">
                                <m-toggle-group v-model:selected="displayMode" class="ml-auto mr-1 hidden md:flex" gap="1" button-style="button">
                                    <m-toggle-group-item :name="0"><i-mdi-view-grid/></m-toggle-group-item>
                                    <m-toggle-group-item :name="1"><i-mdi-view-list/></m-toggle-group-item>
                                    <m-toggle-group-item :name="2"><i-mdi-view-headline/></m-toggle-group-item>
                                </m-toggle-group>
                            </m-input>
                            <m-button v-if="user" to="/user-settings/content">{{ $t('content_settings') }}</m-button>
                        </m-flex>
                    </template>
                </m-dropdown>
            </m-flex>
        </m-flex>

        <m-flex gap="3" class="md:flex-row flex-col">
            <m-content-block v-if="sideFilters" class="mod-filters max-md:!w-full" style="width: 300px;">
                <mod-filters :categories="categories" :refresh-categories="refetchCats" :refresh="refresh" :filters="searchParams" :game="game"/>
            </m-content-block>
            <m-flex column class="flex-1">
                <div v-if="game && currentDisplayCats.length" class="categories-grid mb-3 gap-3">
                    <grid-category v-for="cat of currentDisplayCats" :key="cat.id" :game="game" :category="cat"/>
                </div>
                <m-flex column grow gap="4" class="mods place-content-between" style="flex:1; min-height: 150px;">
                    <m-loading v-if="loading" class="my-auto"/>
                    <template v-else>
                        <mod-list-skeleton
                            :display-mode="(displayMode as number)"
                            :sort-by="sortBy"
                            :no-game="!!game"
                            :error="error"
                            :game="game"
                            :mods="currentMods"
                        />
                        <m-button v-if="hasMore" :loading="loadingButton" color="subtle" @click="loadMore"><i-mdi-chevron-down/> {{$t('load_more')}}</m-button>
                        <h1 v-else-if="currentMods.length == 0" class="m-auto">{{$t('no_mods_found')}}</h1>
                    </template>
                </m-flex>
            </m-flex>
        </m-flex>
    </m-flex>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import type { Category, Game, Mod } from '~~/types/models';
import { longExpiration } from '~~/utils/helpers';
import type { EventHook } from '@vueuse/core';
import { Paginator } from '~~/types/paginator';

const searchBus = useEventBus<string>('search');

const props = withDefaults(defineProps<{
    title?: string,
    titleLink?: string,
    game?: Game,
    userId?: number,
    collab?: boolean,
    triggerRefresh?: EventHook<void>,
    sideFilters?: boolean,
    limit?: number,
    query?: boolean,
    url?: string,
    params?: object,
    initialMods?: Paginator<Mod>
}>(), {
    limit: 20,
    url: 'mods',
    query: false
});

const { user } = useStore();

const query = props.query ? useRouteQuery('query', '') : ref('');

if (props.query) {
    searchBus.on(search => query.value = search);
}

const page = useRouteQuery('page', 1, 'number');
const loadMorePageOverride = ref<number>();
const displayMode = useConsentedCookie('mods-displaymode', { default: () => 0, expires: longExpiration()});
const selectedTags = useRouteQuery('selected-tags', []);
const selectedBlockTags = useRouteQuery('filtered-tags', []);
const loading = ref(false);
const loadingButton = ref(false);
const selectedGame = useRouteQuery('game', props.game?.id, 'number');
const selectedCategories = ref([]);
const selectedCategory = useRouteQuery('category');
const sortBy = useRouteQuery('sort', user?.extra?.default_mods_sort ?? 'bumped_at');
const pages = ref(0);

const fetchPage = computed(() => loadMorePageOverride.value ?? page.value);
const collabComp = computed(() => props.collab ? 1 : 0);

const searchParams = reactive({
    user_id: props.userId,
    page: fetchPage,
    query: query,
    'fields[mods]': listModFields.join(','),
    game_id: selectedGame,
    category_id: selectedCategory,
    tags: selectedTags,
    collab: collabComp,
    // categories: selectedCategories,
    block_tags: selectedBlockTags,
    sort: sortBy,
    limit: computed(() => props.limit),
    ...props.params
});

const gameId = computed(() => props.game?.id ?? searchParams.game_id);

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories`, { 
    immediate: !!searchParams.game_id,
    lazy: true
});

let { data: fetchedMods, refresh, error } = await useFetchMany<Mod>(() => props.url, { 
    params: searchParams,
    immediate: !props.initialMods
});

const currentDisplayCats = computed(() => {
    if (!categories.value) {
        return [];
    }

    const cats: Category[] = [];

    for (const cat of categories.value.data) {
        if (cat.parent_id == selectedCategory.value) {
            cats.push(cat);
        }
    }

    return cats;
});

if (props.initialMods) {
    fetchedMods.value = props.initialMods;
}

if (props.triggerRefresh) {
    props.triggerRefresh.on(() => {
        refresh();
    });
}

let { start: planLoad } = useTimeoutFn(async () => {
    await refresh();
    loading.value = false;
    loadingButton.value = false;
}, 250, { immediate: false });

watch(() => props.url, async () => {
    loading.value = true;
    await refresh();
    loading.value = false;
});

watch(loadMorePageOverride, (newVal) => {
    if (newVal) {
        loadingButton.value = true;
        savedMods.value = currentMods.value;
        fetchedMods.value = null;
    
        loading.value = savedMods.value.length == 0;
        planLoad();
    }
});

watch([
    page,
    query,
    selectedGame,
    selectedCategory,
    selectedTags,
    selectedCategories,
    selectedBlockTags,
    sortBy,
    collabComp
], () => {
    loadMorePageOverride.value = undefined;
    savedMods.value = [];
    loading.value = true;

    planLoad();
});



function setSortBy(sort) {
    sortBy.value = sort;
    refresh();
}

const savedMods = ref<Mod[]>([]);

const hasMore = computed(() => fetchedMods.value ? fetchPage.value < fetchedMods.value.meta.last_page : true);
const currentMods = computed<Mod[]>(() => {
    return fetchedMods.value && [...savedMods.value, ...fetchedMods.value.data] || [...savedMods.value];
});

function loadMore() {
    loadMorePageOverride.value = fetchPage.value + 1;
}
</script>

<style scoped>
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
}

@media(min-width: 768px) {
    .mod-filters {
        width: 280px;
        align-self: self-start;
    } 
}
</style>