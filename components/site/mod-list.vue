<template>
    <flex column gap="3">
        <flex>
            <flex>
                <a-button :disabled="sortBy == 'bumped_at'" icon="clock" @click="setSortBy('bumped_at')">{{$t('last_updated')}}</a-button>
                <a-button :disabled="sortBy == 'published_at'" icon="upload" @click="setSortBy('published_at')">{{$t('published_at')}}</a-button>
                <a-button icon="star" :disabled="sortBy == 'score'" @click="setSortBy('score')">{{$t('popular_now')}}</a-button>
                <Popper arrow>
                    <a-button icon="ellipsis"/>
                    <template #content>
                        <a-dropdown-item icon="heart" @click="setSortBy('likes')">{{$t('likes')}}</a-dropdown-item>
                        <a-dropdown-item icon="download" @click="setSortBy('downloads')">{{$t('downloads')}}</a-dropdown-item>
                        <a-dropdown-item icon="eye" @click="setSortBy('views')">{{$t('views')}}</a-dropdown-item>
                        <a-dropdown-item icon="pencil" @click="setSortBy('name')">{{$t('name')}}</a-dropdown-item>
                    </template>
                </Popper>
            </flex>
            <flex class="ml-auto" gap="3">
                <flex gap="1">
                    <a-button icon="th" :disabled="displayMode == 0" @click="displayMode = 0"/>
                    <a-button icon="list" :disabled="displayMode == 1" @click="displayMode = 1"/>
                    <a-button icon="bars" :disabled="displayMode == 2" @click="displayMode = 2"/>
                </flex>
            </flex>
        </flex>

        <flex column gap="3">
            {{fetchPage}}
            <a-pagination v-if="fetchedMods" v-model="page" v-model:pages="pages" :total="fetchedMods.meta.total" :per-page="40">
                <flex class="ml-auto">
                    <a-button icon="filter" @click="filtersVisible = !filtersVisible"/>
                </flex>
            </a-pagination>
            <h4 v-if="title" class="text-center my-3 text-primary">{{title}}</h4>
            <flex gap="3">
                <flex column gap="4" class="mods place-content-between content-block p-3" style="flex:10; min-height: 250px;">
                    <a-loading v-if="loading" class="my-auto"/>
                    <template v-else>
                        <mod-list-skeleton
                            :display-mode="displayMode"
                            :sort-by="sortBy"
                            :no-game="!!forcedGame"
                            :error="error"
                            :mods="currentMods"
                        />
                        <a-button v-if="hasMore" color="subtle" icon="chevron-down" @click="loadMore">{{$t('load_more')}}</a-button>
                        <h1 v-else-if="currentMods.length == 0" class="text-center my-auto">{{$t('no_mods_found')}}</h1>
                    </template>
                </flex>
                <content-block v-if="filtersVisible" class="self-start" style="flex:2;">
                    <a-input v-model="query" label="Search"/>
                    <flex v-if="categories && categories.data.length" column>
                        <span>{{$t('categories')}}</span>
                        <category-tree :categories="categories.data" set-query/>
                    </flex>
                    <a-select v-if="!forcedGame" v-model="selectedGame" label="Game" placeholder="Any game" clearable :options="store.games?.data" @update:model-value="gameChanged"/>
                    <a-select v-model="selectedTags" label="Tags" placeholder="Select Tags" multiple clearable :options="tags.data" max-selections="10"/>
                    <a-select v-model="selectedBlockTags" label="Filter Out Tags" placeholder="Select Tags" multiple clearable :options="tags.data" max-selections="10"/>
                </content-block>
            </flex>
        </flex>
    </flex>
</template>
<script setup lang="ts">
import { DateTime } from 'luxon';
import { Category, Mod, Tag } from '~~/types/models';
import { useStore } from '~~/store';

const props = defineProps({
    title: String,
    forcedGame: Number,
    userId: Number,
    url: {
        type: String,
        default: 'mods'
    }
});

const store = useStore();

const query = useRouteQuery('query', '');
const page = useRouteQuery('page', 1, val => parseInt(val));
const pageOverride = ref(null);
const displayMode = useCookie('mods-displaymode', { default: () => 0, expires: DateTime.now().plus({ years: 99 }).toJSDate()});
const selectedTags = useRouteQuery('selected-tags', []);
const selectedBlockTags = useRouteQuery('filtered-tags', []);
const loading = ref(false);
const selectedGame = useRouteQuery('game', props.forcedGame?.toString());
const selectedCategories = ref([]);
const selectedCategory = useRouteQuery('category');
const sortBy = useRouteQuery('sort-by', 'bumped_at');
const pages = ref(0);
const filtersVisible = ref(true);

await store.fetchGames();

const { data: tags } = await useFetchMany<Tag>('tags', { params: { type: 'mod' } });

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${selectedGame.value}/categories?include_paths=1`, { immediate: !!selectedGame.value });

const fetchPage = computed(() => pageOverride.value ?? page.value);

const searchParams = reactive({
    limit: 40,
    user_id: props.userId,
    page: fetchPage,
    query: query,
    game_id: selectedGame,
    category_id: selectedCategory,
    tags: selectedTags,
    categories: selectedCategories,
    block_tags: selectedBlockTags,
    sort_by: sortBy
});

const { data: fetchedMods, refresh, error } = await useFetchMany<Mod>(() => props.url, { 
    params: searchParams
});

let { start: planLoad } = useTimeoutFn(async () => {
    await refresh();
    loading.value = false;
}, 250, { immediate: false });

watch(() => props.url, async () => {
    loading.value = true;
    await refresh();
    loading.value = false;
});

watch(page, () =>  {
    savedMods.value = [];
    pageOverride.value = null;
});

watch([page, pageOverride, searchParams], (value, newValue) => {
    loading.value = savedMods.value.length == 0;

    if (loading.value && value[0] == newValue[0]) {
        page.value = undefined;
    }

    planLoad();
});

async function gameChanged() {
    await refresh();

    if (selectedGame.value) {
        await refetchCats();
    } else {
        categories.value = null;
    }
}

function setSortBy(sort) {
    sortBy.value = sort;
    refresh();
}

const savedMods = ref<Mod[]>([]);

const hasMore = computed(() => fetchPage.value < fetchedMods.value.meta.last_page); //TODO: actually detect when there's no more
const currentMods = computed<Mod[]>(() => {
    return fetchedMods.value && [...savedMods.value, ...fetchedMods.value.data] || [...savedMods.value];
});

function loadMore() {
    
    const save = currentMods.value;
    pageOverride.value = fetchPage.value + 1;
    console.log(fetchPage.value + 1);
    console.log(pageOverride.value);

    savedMods.value = save;
}
</script>