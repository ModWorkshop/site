<template>
    <flex column gap="3">
        <flex>
            <flex>
                <a-button @click="setSortBy('bump_date')" icon="clock" :no-bg="sortBy != 'bump_date'">{{$t('last_updated')}}</a-button>
                <a-button @click="setSortBy('publish_date')" icon="upload" :no-bg="sortBy != 'publish_date'">{{$t('publish_date')}}</a-button>
                <a-button @click="setSortBy('score')" icon="star" :no-bg="sortBy != 'score'">{{$t('popular_now')}}</a-button>
                <Popper arrow>
                    <a-button icon="ellipsis" :no-bg="!['likes', 'downloads', 'views', 'name'].includes(sortBy)"/>
                    <template #content>
                        <a-dropdown-item icon="heart" @click="setSortBy('likes')">Likes</a-dropdown-item>
                        <a-dropdown-item icon="download" @click="setSortBy('downloads')">Downloads</a-dropdown-item>
                        <a-dropdown-item icon="eye" @click="setSortBy('views')">Views</a-dropdown-item>
                        <a-dropdown-item icon="pencil" @click="setSortBy('name')">Name</a-dropdown-item>
                    </template>
                </Popper>
            </flex>
            <flex class="ml-auto" gap="3">
                <flex gap="1">
                    <a-button icon="th"/>
                    <a-button icon="list"/>
                    <a-button icon="bars"/>
                </flex>
            </flex>
        </flex>

        <flex column gap="3">
            <flex gap="1">
                <template v-if="pages > 5">
                    <a-button v-if="page > 3" @click="setPage(1, true)">1</a-button>
                    <a-button disabled v-if="pageNumbers[0] > 2">...</a-button>
                </template>
                <a-button :disabled="page == n" @click="setPage(n, true)" v-for="n in pageNumbers">
                    {{n}}
                </a-button>
                <template v-if="pages > 5">
                    <a-button v-if="pages - pageNumbers[pageNumbers.length-1] > 1" disabled>...</a-button>
                    <a-button v-if="pages - page > 2" @click="setPage(pages, true)">{{pages}}</a-button>
                </template>
            </flex>
            <h4 v-if="title" class="text-center my-3 text-primary">{{title}}</h4>
            <flex gap="6">
                <flex wrap column gap="3" class="mods justify-content-start" style="flex:10;">
                    <div v-if="isList" id="mod_list_head" class="p-3 list_mod align-items-center content-bg" style="height:40px;">
                        <div id="thumbnail" class="{% if cookies.mods_displaymode == 3 %} d-none{% endif %}" style="min-width: 200px;"></div>
                        <div class="ml-2" style="flex: 4;">{{$t('mod_name')}}</div>
                        <div style="flex: 3">{{$t('author')}}</div>
                        <div v-if="type != 3" style="flex: 3">{{type == 2 ? $t('category') : $t('game_category')}}</div>
                        <div>{{$t('likes')}}</div>
                        <div>{{$t('downloads')}}</div>
                        <div>{{$t('download_views')}}</div>
                        <div v-if="justDate" style="flex: 2;">{{$t('date')}}</div>
                        <template v-else>
                            <div id="date" style="flex: 2;">{{$t('last_updated')}}</div>
                            <div id="pub-date" class="d-none" style="flex: 2;">{{$t('publish_date')}}</div>
                        </template>
                    </div>
                    <div id="content" :class="`mods ${isList ? 'mods-list' : 'mods-grid'}`">
                        <div v-if="error">
                            There was an error fetching mods
                            {{error}}
                        </div>
                        <template v-else>
                            <a-mod v-for="mod in currentMods" :key="mod.id" :mod="mod" :noGame="forcedGame" :sort="sortBy"/>
                        </template>
                    </div>
                    <a-button v-if="hasMore" id="load-more" color="none" icon="chevron-down" @click="() => incrementPage()">{{$t('load_more')}}</a-button>
                    <span v-else-if="currentMods.length == 0" class="text-center">
                        No mods found
                    </span>
                </flex>
                <content-block class="self-start" style="flex:2;">
                    <group label="Search">
                        <a-input type="text" v-model="query"/>
                    </group>
                    <group v-if="!forcedGame" label="Game">
                        <a-select v-model="selectedGame" placeholder="Any game" clearable :options="store.games.data" @update="gameChanged"/>
                    </group>
                    <group label="Categories">
                        <a-select v-model="selectedCategories" placeholder="Select categories" multiple :disabled="!selectedGame" :options="selectedGame && categories?.data || []" @update="refresh"/>
                    </group>
                    <group label="Tags">
                        <a-select v-model="selectedTags" placeholder="Select Tags" multiple :options="tags"/>
                    </group>
                    <group label="Filter Out Tags">
                        <a-select v-model="selectedBlockTags" placeholder="Select Tags" multiple :options="tags"/>
                    </group>
                    <!-- <a-button color="none" icon="ellipsis-v"/> -->
                </content-block>
            </flex>
        </flex>
    </flex>
</template>
<script setup>
    import { useStore } from '../../store';

    const sortOptions = [
        'Last Updated',
        'Popularity',
        'Likes',
        'Downloads',
        'Views',
        'Name',
        'Publish Date',
    ];

    const props = defineProps({
        title: String,
        forcedGame: Number,
        userId: Number
    });

    const store = useStore();

    const query = ref('');
    const page = ref(1);
    const isList = ref(false);
    const justDate = ref(false);
    const selectedTags = ref([]);
    const selectedBlockTags = ref([]);
    const loading = ref(true);
    const tags = computed(() => store.tags);
    const selectedGame = ref(props.forcedGame);
    const selectedCategories = ref([]);
    const sortBy = ref('bump_date');

    await store.fetchGames();
    await store.fetchTags();

    const { data: fetchedMods, refresh, error } = await useAsyncDyn('get-mods', () => useGet('mods', { 
        params: {
            submitter_id: props.userId,
            page: page.value,
            query: query.value,
            submitter_id: props.userId,
            game_id: selectedGame.value,
            tags: selectedTags.value,
            categories: selectedCategories.value,
            block_tags: selectedBlockTags.value,
            sort_by: sortBy.value
        }
    }));

    const { data: categories, refresh: refetchCats } = await useAPIFetch(() => `games/${selectedGame.value}/categories`);
    
    function gameChanged() {
        refetchCats();
        refresh();
    }

    function setSortBy(sort) {
        sortBy.value = sort;
        refresh();
    }

    const savedMods = ref([]);
    const pages = computed(() => parseInt(fetchedMods.value?.meta?.total / 40 || 0));
    const pageNumbers = computed(() => {
        if (page.value < 4) {
            return [...Array(Math.min(5, pages.value)).keys()].map(x => x + 1);
        } else if (pages.value - page.value > 2) {
            return [...Array(5).keys()].map(x => x + page.value-2);
        } else {
            return [...Array(5).keys()].map(x => pages.value - 4 + x);
        }
    });
    const hasMore = computed(() => pages.value > 0); //TODO: actually detect when there's no more
    const currentMods = computed(() => {
        return fetchedMods.value && [...savedMods.value, ...fetchedMods.value.data] || []
    });

    let lastTimeout = null;
    watch([query, selectedTags, selectedBlockTags], () => {
        if (lastTimeout) {
            clearTimeout(lastTimeout);
            lastTimeout = null;
        }
        lastTimeout = setTimeout(refresh, 250);
    });

    function incrementPage() {
        setPage(page.value++, false);
    }

    async function setPage(newPage, reload) {
        page.value = newPage;
        if (reload) {
            savedMods.value = [];
        }
        savedMods.value = currentMods.value;
        loading.value = true;
        await refresh();
        loading.value = false;
    }
</script>