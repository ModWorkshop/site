<template>
    <flex column gap="3">
        <span v-if="title" class="h2">
            <NuxtLink class="text-body" :to="titleLink">{{title}}</NuxtLink>
        </span>
        <slot name="buttons"/>
        <flex wrap class="overflow-auto">
            <flex>
                <a-button :disabled="sortBy == 'bumped_at'" @click="setSortBy('bumped_at')">
                    <i-mdi-clock/> {{$t('last_updated')}}
                </a-button>
                <a-button :disabled="sortBy == 'published_at'" @click="setSortBy('published_at')">
                    <i-mdi-upload/> {{$t('published_at')}}
                </a-button>
                <VDropdown>
                    <a-button><i-mdi-star/> {{$t('popularity')}}</a-button>
                    <template #popper>
                        <button-group v-model:selected="sortBy" column>
                            <a-group-button name="score"><i-mdi-calendar-month/> {{$t('popular_monthly')}}</a-group-button>
                            <a-group-button name="weekly_score"><i-mdi-calendar-week/> {{$t('popular_weekly')}}</a-group-button>
                            <a-group-button name="daily_score"><i-mdi-calendar/> {{$t('popular_today')}}</a-group-button>
                        </button-group>
                    </template>
                </VDropdown>
                <VDropdown>
                    <a-button>
                        <i-mdi-dots-vertical/>
                    </a-button>
                    <template #popper>
                        <flex column>
                            <button-group v-model:selected="sortBy" column>
                                <a-group-button name="best_match"><i-mdi-magnify/> {{$t('best_match')}} </a-group-button>
                                <a-group-button name="random" @click="sortBy == 'random' && refresh()"><i-mdi-dice/> {{$t('random')}}</a-group-button>
                                <a-group-button name="likes"><i-mdi-heart/> {{$t('likes')}}</a-group-button>
                                <a-group-button name="downloads"><i-mdi-download/> {{$t('downloads')}}</a-group-button>
                                <a-group-button name="views"><i-mdi-eye/>{{$t('views')}}</a-group-button>
                                <a-group-button name="name"><i-mdi-pencil/> {{$t('name')}}</a-group-button>
                            </button-group>
                        </flex>
                    </template>
                </VDropdown>
                <flex v-if="!sideFilters">
                    <VDropdown>
                        <a-button><i-mdi-filter/></a-button>
                        <template #popper>
                            <Suspense>
                                <flex class="p-4" gap="3" column style="width: 300px;">
                                    <mod-filters :refresh="refresh" :filters="searchParams" :game="game"/>
                                </flex>
                            </Suspense>
                        </template>
                    </VDropdown>
                </flex>
            </flex>
            
            <flex class="ml-auto" gap="2">
                <a-pagination v-if="fetchedMods" v-model="page" v-model:pages="pages" :total="fetchedMods.meta.total" :per-page="fetchedMods.meta.per_page" no-hiding/>
                <VDropdown>
                    <a-button><i-mdi-cog/></a-button>
                    <template #popper>
                        <flex column class="p-2">
                            <a-input :label="$t('display_mode')">
                                <button-group v-model:selected="displayMode" class="ml-auto mr-1 hidden md:flex" gap="1" button-style="button">
                                    <a-group-button :name="0"><i-mdi-view-grid/></a-group-button>
                                    <a-group-button :name="1"><i-mdi-view-list/></a-group-button>
                                    <a-group-button :name="2"><i-mdi-view-headline/></a-group-button>
                                </button-group>
                            </a-input>
                        </flex>
                    </template>
                </VDropdown>
            </flex>
        </flex>

        <flex column gap="3">
            <flex gap="3" class="md:flex-row flex-col">
                <content-block v-if="sideFilters" class="mod-filters" style="width: 300px;">
                    <mod-filters :refresh="refresh" :filters="searchParams" :game="game"/>
                </content-block>
                <flex column grow gap="3">
                    <flex column gap="4" class="mods place-content-between" style="flex:1; min-height: 150px;">
                        <a-loading v-if="loading" class="my-auto"/>
                        <template v-else>
                            <mod-list-skeleton
                                :display-mode="displayMode"
                                :sort-by="sortBy"
                                :no-game="!!game"
                                :error="error"
                                :game="game"
                                :mods="currentMods"
                            />
                            <a-button v-if="hasMore" :loading="loadingButton" color="subtle" @click="loadMore"><i-mdi-chevron-down/> {{$t('load_more')}}</a-button>
                            <h1 v-else-if="currentMods.length == 0" class="m-auto">{{$t('no_mods_found')}}</h1>
                        </template>
                    </flex>
                </flex>
            </flex>
        </flex>
    </flex>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { Game, Mod } from '~~/types/models';
import { longExpiration } from '~~/utils/helpers';
import { EventHook } from '@vueuse/core';

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
    query: boolean,
    url?: string,
    params?: object
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

const { data: fetchedMods, refresh, error } = await useFetchMany<Mod>(() => props.url, { 
    params: searchParams
});

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

<style>
@media(min-width: 768px) {
    .mod-filters {
        width: 280px;
        align-self: self-start;
    } 
}
</style>