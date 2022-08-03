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
                        <a-dropdown-item icon="heart" @click="setSortBy('likes')">Likes</a-dropdown-item>
                        <a-dropdown-item icon="download" @click="setSortBy('downloads')">Downloads</a-dropdown-item>
                        <a-dropdown-item icon="eye" @click="setSortBy('views')">Views</a-dropdown-item>
                        <a-dropdown-item icon="pencil" @click="setSortBy('name')">Name</a-dropdown-item>
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
            <a-pagination v-model="page" v-model:pages="pages" :total="fetchedMods.meta.total" per-page="40" @update="page => setPage(page, true)"/>
            <h4 v-if="title" class="text-center my-3 text-primary">{{title}}</h4>
            <flex gap="6">
                <flex column class="mods justify-content-start content-block p-2" style="flex:10;">
                    <div v-if="displayMode == 0" class="mods mods-grid gap-2">
                        <div v-if="error">
                            There was an error fetching mods
                            {{error}}
                        </div>
                        <template v-else>
                            <a-mod v-for="mod in currentMods" :key="mod.id" :mod="mod" :no-game="!!forcedGame" :sort="sortBy"/>
                        </template>
                    </div>
                    <table v-else style="border-spacing: 0.5rem 0.25rem;">
                        <thead>
                            <tr>
                                <th v-if="displayMode == 1">{{$t('thumbnail')}}</th>
                                <th>{{$t('mod_name')}}</th>
                                <th>{{$t('owner')}}</th>
                                <th>{{!!forcedGame ? $t('category') : $t('game_category')}}</th>
                                <th>{{$t('likes')}}</th>
                                <th>{{$t('downloads')}}</th>
                                <th>{{$t('views')}}</th>
                                <th>{{sortBy == 'published_at' ? $t('published_at') : $t('last_updated')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <mod-row v-for="mod in currentMods" :key="mod.id" :mod="mod" :no-game="!!forcedGame" :sort="sortBy" :display-mode="displayMode"/>
                        </tbody>
                    </table>
                    <a-button v-if="hasMore" id="load-more" color="none" icon="chevron-down" @click="() => incrementPage()">{{$t('load_more')}}</a-button>
                    <span v-else-if="currentMods.length == 0" class="text-center">
                        No mods found
                    </span>
                </flex>
                <content-block class="self-start" style="flex:2;">
                    <a-input v-model="query" label="Search" type="text"/>
                    <a-select v-if="!forcedGame" v-model="selectedGame" label="Game" placeholder="Any game" clearable :options="store.games?.data" @update:model-value="gameChanged"/>
                    <a-select v-model="selectedCategories" label="Categories" text-by="path" placeholder="Select categories" multiple :disabled="!selectedGame" :options="categories && categories.data" @update:model-value="refresh"/>
                    <a-select v-model="selectedTags" label="Tags" placeholder="Select Tags" multiple :options="tags.data"/>
                    <a-select v-model="selectedBlockTags" label="Filter Out Tags" placeholder="Select Tags" multiple :options="tags.data"/>
                    <!-- <a-button color="none" icon="ellipsis-v"/> -->
                </content-block>
            </flex>
        </flex>
    </flex>
</template>
<script setup lang="ts">
import { Category, Mod, Tag } from '~~/types/models';
import { useStore } from '../../store';
import { useStorage } from '@vueuse/core';

const props = defineProps({
    title: String,
    forcedGame: Number,
    userId: Number
});

const store = useStore();

const query = ref('');
const page = ref(1);
const displayMode = await useStorage('mods-displaymode');
const selectedTags = ref([]);
const selectedBlockTags = ref([]);
const loading = ref(true);
const selectedGame = ref(props.forcedGame);
const selectedCategories = ref([]);
const sortBy = ref('bumped_at');
const pages = ref(0);

await store.fetchGames();

const { data: tags } = await useFetchMany<Tag>(() => 'tags');

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${selectedGame.value}/categories?include_paths=1`, { immediate: !!selectedGame.value });

const { data: fetchedMods, refresh, error } = await useAsyncData('get-mods', () => useGetMany<Mod>('mods', { 
    params: {
        limit: 40,
        user_id: props.userId,
        page: page.value,
        query: query.value,
        game_id: selectedGame.value,
        tags: selectedTags.value,
        categories: selectedCategories.value,
        block_tags: selectedBlockTags.value,
        sort_by: sortBy.value
    }
}), { initialCache: false });

    
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
    return fetchedMods.value && [...savedMods.value, ...fetchedMods.value.data] || [];
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