<template>
    <flex column gap="3">
        <flex>
            <flex>
                <a-button @click="setSortBy('bump_date')" icon="clock" :disabled="sortBy == 'bump_date'">{{$t('last_updated')}}</a-button>
                <a-button @click="setSortBy('publish_date')" icon="upload" :disabled="sortBy == 'publish_date'">{{$t('publish_date')}}</a-button>
                <a-button @click="setSortBy('score')" icon="star" :disabled="sortBy == 'score'">{{$t('popular_now')}}</a-button>
                <Popper arrow>
                    <a-button icon="ellipsis"/>
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
                    <a-button icon="th" :disabled="true"/>
                    <a-button icon="list" :disabled="false"/>
                    <a-button icon="bars" :disabled="false"/>
                </flex>
            </flex>
        </flex>

        <flex column gap="3">
            <a-pagination v-model="page" :total="fetchedMods.meta.total" perPage="40" @update="page => setPage(page, true)" v-model:pages="pages"/>
            <h4 v-if="title" class="text-center my-3 text-primary">{{title}}</h4>
            <flex gap="6">
                <flex wrap column gap="3" class="mods justify-content-start" style="flex:10;">
                    <div v-if="isList" id="mod_list_head" class="p-3 list_mod align-items-center content-bg" style="height:40px;">
                        <div id="thumbnail" class="{% if cookies.mods_displaymode == 3 %} d-none{% endif %}" style="min-width: 200px;"></div>
                        <div class="ml-2" style="flex: 4;">{{$t('mod_name')}}</div>
                        <div style="flex: 3">{{$t('author')}}</div>
                        <!-- <div v-if="type != 3" style="flex: 3">{{type == 2 ? $t('category') : $t('game_category')}}</div> -->
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
                            <a-mod v-for="mod in currentMods" :key="mod.id" :mod="mod" :noGame="!!forcedGame" :sort="sortBy"/>
                        </template>
                    </div>
                    <a-button v-if="hasMore" id="load-more" color="none" icon="chevron-down" @click="() => incrementPage()">{{$t('load_more')}}</a-button>
                    <span v-else-if="currentMods.length == 0" class="text-center">
                        No mods found
                    </span>
                </flex>
                <content-block class="self-start" style="flex:2;">
                    <a-input label="Search" type="text" v-model="query"/>
                    <a-select v-if="!forcedGame" label="Game" v-model="selectedGame" placeholder="Any game" clearable :options="store.games" @update="gameChanged"/>
                    <a-select label="Categories" v-model="selectedCategories" placeholder="Select categories" multiple :disabled="!selectedGame" :options="categories && categories.data" @update="refresh"/>
                    <a-select label="Tags" v-model="selectedTags" placeholder="Select Tags" multiple :options="tags"/>
                    <a-select label="Filter Out Tags" v-model="selectedBlockTags" placeholder="Select Tags" multiple :options="tags"/>
                    <!-- <a-button color="none" icon="ellipsis-v"/> -->
                </content-block>
            </flex>
        </flex>
    </flex>
</template>
<script setup lang="ts">
    import { Category, Mod } from '~~/types/models';
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
    const pages = ref(0);

    await store.fetchGames();
    await store.fetchTags();

    const { data: fetchedMods, refresh, error } = await useAsyncData('get-mods', () => useGetMany<Mod>('mods', { 
        params: {
            submitter_id: props.userId,
            page: page.value,
            query: query.value,
            game_id: selectedGame.value,
            tags: selectedTags.value,
            categories: selectedCategories.value,
            block_tags: selectedBlockTags.value,
            sort_by: sortBy.value
        }
    }), { initialCache: false });

    const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${selectedGame.value}/categories`, { immediate: !!selectedGame.value });
    
    function gameChanged() {
        if (selectedGame.value) {
            refetchCats();
        } else {
            categories.value = null;
        }
        refresh();
    }

    function setSortBy(sort) {
        sortBy.value = sort;
        refresh();
    }

    const savedMods = ref<Mod[]>([]);

    const hasMore = computed(() => pages.value > 0); //TODO: actually detect when there's no more
    const currentMods = computed<Mod[]>(() => {
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

    async function setPage(newPage: number, reload=false) {
        page.value = newPage;
        if (reload) {
            savedMods.value = [];
        } else {
            savedMods.value = currentMods.value;
        }
        loading.value = true;
        await refresh();
        loading.value = false;
    }
</script>