<template>
    <flex column gap="3">
        <h2 v-if="title">
            <NuxtLink class="text-body" :to="titleLink">{{title}}</NuxtLink>
        </h2>
        <slot name="buttons"/>
        <flex wrap class="overflow-auto">
            <a-button :disabled="sortBy == 'bumped_at'" icon="mdi:clock" @click="setSortBy('bumped_at')">{{$t('last_updated')}}</a-button>
            <a-button :disabled="sortBy == 'published_at'" icon="mdi:upload" @click="setSortBy('published_at')">{{$t('published_at')}}</a-button>
            <VDropdown>
                <a-button icon="mdi:star">{{$t('popularity')}}</a-button>
                <template #popper>
                    <button-group v-model:selected="sortBy" column>
                        <a-group-button name="score" icon="calendar">{{$t('popular_monthly')}}</a-group-button>
                        <a-group-button name="weekly_score" icon="calendar-week">{{$t('popular_weekly')}}</a-group-button>
                        <a-group-button name="daily_score" icon="calendar-days">{{$t('popular_today')}}</a-group-button>
                    </button-group>
                </template>
            </VDropdown>
            <VDropdown v-if="!sideFilters">
                <a-button icon="mdi:dots-vertical"/>
                <template #popper>
                    <flex column>
                        <button-group v-model:selected="sortBy" column>
                            <a-group-button icon="mdi:dice" name="random" @click="sortBy == 'random' && refresh()">{{$t('random')}}</a-group-button>
                            <a-group-button icon="mdi:heart" name="likes">{{$t('likes')}}</a-group-button>
                            <a-group-button icon="mdi:download" name="downloads">{{$t('downloads')}}</a-group-button>
                            <a-group-button icon="mdi:eye" name="views">{{$t('views')}}</a-group-button>
                            <a-group-button icon="mdi:pencil" name="name">{{$t('name')}}</a-group-button>
                        </button-group>
                    </flex>
                </template>
            </VDropdown>
            <button-group v-model:selected="displayMode" class="ml-auto mr-1 hidden md:flex" gap="1" button-style="button">
                <a-group-button icon="mdi:view-grid" :name="0"/>
                <a-group-button icon="mdi:view-list" :name="1"/>
                <a-group-button icon="mdi:view-headline" :name="2"/>
            </button-group>
            <flex v-if="!sideFilters">
                <VDropdown>
                    <a-button icon="mdi:filter"/>
                    <template #popper>
                        <Suspense>
                            <flex class="p-4" gap="3" column style="width: 300px">
                                <mod-filters :refresh="refresh" :filters="searchParams" :game="game"/>
                            </flex>
                        </Suspense>
                    </template>
                </VDropdown>
            </flex>
        </flex>

        <flex column gap="3">
            <a-pagination v-if="fetchedMods" v-model="page" v-model:pages="pages" :total="fetchedMods.meta.total" :per-page="40"/>
            <flex gap="3">
                <content-block v-if="sideFilters" class="self-start" style="flex:2; max-width: 280px;">
                    <mod-filters :refresh="refresh" :filters="searchParams" :game="game"/>
                </content-block>
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
                        <a-button v-if="hasMore" :loading="loadingButton" color="subtle" icon="chevron-down" @click="loadMore">{{$t('load_more')}}</a-button>
                        <h1 v-else-if="currentMods.length == 0" class="m-auto">{{$t('no_mods_found')}}</h1>
                    </template>
                </flex>
            </flex>
        </flex>
    </flex>
</template>
<script setup lang="ts">
import { EventRaiser } from '~~/composables/useEventRaiser';
import { useStore } from '~~/store';
import { Game, Mod } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { longExpiration } from '~~/utils/helpers';

const props = withDefaults(defineProps<{
    title?: string,
    titleLink?: string,
    game?: Game,
    userId?: number,
    collab?: boolean,
    triggerRefresh?: EventRaiser,
    sideFilters?: boolean,
    limit?: number,
    url?: string
}>(), {
    limit: 40,
    url: 'mods'
});

const emit = defineEmits<{
    (e: 'fetched', mods: Paginator<Mod>)
}>();

const { user } = useStore();

const query = useRouteQuery('query', '');
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
const collab = computed(() => props.collab ? 1 : 0);

const searchParams = reactive({
    user_id: props.userId,
    page: fetchPage,
    query: query,
    game_id: selectedGame,
    category_id: selectedCategory,
    tags: selectedTags,
    collab,
    categories: selectedCategories,
    block_tags: selectedBlockTags,
    sort: sortBy,
    limit: computed(() => props.limit)
});

const { data: fetchedMods, refresh, error } = await useFetchMany<Mod>(() => props.url, { 
    params: searchParams
});

if (props.triggerRefresh) {
    watch(props.triggerRefresh.listen, refresh);
}

watch(fetchedMods, val => emit('fetched', val), { immediate: true });

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
    collab
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