<template>
    <m-flex column gap="3">
        <NuxtLink v-if="title" class="h2 text-body" :to="titleLink">{{title}}</NuxtLink>
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
                            <m-toggle-group v-model:selected="sortBy" column button-style="dropdown">
                                <m-toggle-group-item value="score"><i-mdi-calendar-month/> {{$t('popular_monthly')}}</m-toggle-group-item>
                                <m-toggle-group-item value="weekly_score"><i-mdi-calendar-week/> {{$t('popular_weekly')}}</m-toggle-group-item>
                                <m-toggle-group-item value="daily_score"><i-mdi-calendar/> {{$t('popular_today')}}</m-toggle-group-item>
                            </m-toggle-group>
                        </template>
                    </m-dropdown>
                    <m-dropdown>
                        <m-button :title="$t('sort_by')">
                            <i-mdi-dots-vertical/>
                        </m-button>
                        <template #content>
                            <m-flex column>
                                <m-toggle-group v-model:selected="sortBy" column button-style="dropdown">
                                    <m-toggle-group-item value="best_match"><i-mdi-magnify/> {{$t('best_match')}} </m-toggle-group-item>
                                    <m-toggle-group-item value="random" @click="sortBy == 'random' && refresh()"><i-mdi-dice/> {{$t('random')}}</m-toggle-group-item>
                                    <m-toggle-group-item value="likes"><i-mdi-heart/> {{$t('likes')}}</m-toggle-group-item>
                                    <m-toggle-group-item value="downloads"><i-mdi-download/> {{$t('downloads')}}</m-toggle-group-item>
                                    <m-toggle-group-item value="views"><i-mdi-eye/>{{$t('views')}}</m-toggle-group-item>
                                    <m-toggle-group-item value="name"><i-mdi-pencil/> {{$t('name')}}</m-toggle-group-item>
                                </m-toggle-group>
                            </m-flex>
                        </template>
                    </m-dropdown>
                    <m-flex v-if="!sideFilters">
                        <m-dropdown :close-on-click="false">
                            <m-button><i-mdi-filter/></m-button>
                            <template #content>
                                <m-flex class="p-4" gap="3" column style="width: 300px;">
                                    <Suspense>
                                        <template #fallback>
                                            <m-loading/>
                                        </template>
                                        <mod-filters :categories="currCategories" :refresh-categories="refetchCats" :refresh="refresh" :filters="searchParams" :game="game"/>
                                    </Suspense>
                                </m-flex>
                            </template>
                        </m-dropdown>
                    </m-flex>
                </m-flex>
            </m-flex>
            
            <m-flex class="md:ml-auto" gap="2">
                <m-pagination v-if="fetchedMods" v-model="page" v-model:pages="pages" :total="fetchedMods.meta.total" :per-page="fetchedMods.meta.per_page" no-hiding/>
                <m-dropdown>
                    <m-button :title="$t('settings')"><i-mdi-cog/></m-button>
                    <template #content>
                        <m-flex column class="p-2" gap="2">
                            <m-input :label="$t('display_mode')">
                                <m-toggle-group v-model:selected="displayMode" class="ml-auto mr-1" gap="1" button-style="button">
                                    <m-toggle-group-item :value="0"><i-mdi-view-grid/></m-toggle-group-item>
                                    <m-toggle-group-item :value="1"><i-mdi-view-list/></m-toggle-group-item>
                                    <m-toggle-group-item :value="2"><i-mdi-view-headline/></m-toggle-group-item>
                                </m-toggle-group>
                            </m-input>
                            <m-button v-if="user" to="/user-settings/content">{{ $t('content_settings') }}</m-button>
                        </m-flex>
                    </template>
                </m-dropdown>
            </m-flex>
        </m-flex>

        <m-flex gap="3" class="md:flex-row flex-col">
            <m-flex v-if="sideFilters" class="max-md:!w-full items-center" column gap="3" style="width: 300px;">
                <m-content-block class="mod-filters w-full">
                    <mod-filters :categories="currCategories" :refresh-categories="refetchCats" :refresh="refresh" :filters="searchParams" :game="game"/>
                </m-content-block>
                <div id="mws-ads-filters" class="mb-8"/>
            </m-flex>
            <m-flex column class="flex-1">
                <div v-if="game && currentDisplayCats.length" class="categories-grid mb-3 gap-3">
                    <grid-category v-for="cat of currentDisplayCats" :key="cat.id" :game="game" :category="cat"/>
                </div>
                <m-flex column grow gap="4" class="mods" style="flex:1; min-height: 150px;">
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
                        <m-button v-if="hasMore" :loading="loadingButton" color="subtle" @click="loadMore">{{$t('load_more')}} <i-mdi-chevron-down/></m-button>
                        <h3 v-else-if="currentMods.length == 0" class="mx-auto">{{$t('no_mods_found')}}</h3>
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
    categories?: Category[],
    initialMods?: Paginator<Mod>
}>(), {
    limit: 20,
    url: 'mods',
    query: false
});

const { user } = useStore();

if (props.sideFilters) {
    useInsertAd('mws-ads-filters', {
        sizes: [[ "300", "250" ]],
        renderVisibleOnly: true,
        report: {
            position: "bottom-left"
        }
    });
}

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

const { data: fetchCategories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories`, { 
    immediate: !!searchParams.game_id && !props.categories,
    lazy: true
});

let { data: fetchedMods, refresh, error } = await useFetchMany<Mod>(() => props.url, { 
    params: searchParams,
    immediate: !props.initialMods
});

const currCategories = computed(() => fetchCategories.value?.data ?? props.categories);

const currentDisplayCats = computed(() => {
    if (!currCategories.value) {
        return [];
    }

    const cats: Category[] = [];

    for (const cat of currCategories.value) {
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
</style>