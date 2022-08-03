<template>
    <a-form :model="category" :can-save="canSaveOverride" :created="category.id != -1" float-save-gui @submit="save">
        <flex column gap="3">
            <div>
                <a-button icon="arrow-left" to="/admin/categories">Back to Categories</a-button>
            </div>
            <img-uploader id="thumbnail" v-model="thumbnailBlob" label="Thumbnail" :src="(category.thumbnail && `categories/thumbnails/${category.thumbnail}`) || 'assets/nopreview.webp'">
                <template #label="{ src }">
                    <a-img class="round" :src="src"/>
                </template>
            </img-uploader>
            <a-input v-model="category.name" label="Name"/>
            <a-input v-model="category.webhook_url" :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)"/>
            <a-input v-model="category.approval_only" :label="$t('approval_only')" type="checkbox" desc="Whether or not mods uploaded to this category need to first be approved by a moderator"/>
            <!-- <md-editor :label="$t('description')" v-model="category.desc"/> -->
            <flex>
                <a-select v-model="category.game_id" label="Game" placeholder="Select a game" clearable :options="games.data"/>
                <a-select v-model="category.parent_id" label="Parent Category" placeholder="Select a parent category" clearable :options="categories"/>
            </flex>
            <va-alert class="w-full" color="warning">
                <details>
                    <summary>DANGER ZONE</summary>
                    <div class="p-4 mt-2">
                        <a-button color="danger">Delete</a-button>
                    </div>
                </details>
            </va-alert>
        </flex>
    </a-form>
</template>

<script setup lang="ts">
import { Ref } from "vue";
import { useStore } from "~~/store";
import { Category } from "~~/types/models";

const store = useStore();

const categoryTemplate: Category = {
    id: -1,
    name: '',
    game_id: null,
    parent_id: null,
    short_name: "",
    desc: "",
    hidden: false,
    grid: false,
    disporder: 0,
    thumbnail: "",
    banner: "",
    buttons: "",
    webhook_url: "",
    approval_only: false,
    last_date: "",
    created_at: "",
    updated_at: ""
};

let category: Ref<Category>;

await store.fetchGames();

const categories = ref<Category[]>([]);
const games = store.games;
const route = useRoute();
const thumbnailBlob = ref(null);
const canSaveOverride = computed(() => !!thumbnailBlob.value);

if (route.params.id == 'new') {
    category = ref<Category>(categoryTemplate);
}
else if (route.params.id) {
    const { data } = await useFetchData<Category>(`categories/${route.params.id}`);
    category = data;
}

watch(() => category.value.game_id, async () => {
    if (category.value.game_id) {
        const cats = await useGetMany<Category>(`/games/${category.value.game_id}/categories?include_paths=1`);
        categories.value = cats.data;
    } else {
        categories.value = [];
    }
}, {immediate: true});

async function save() {
    try {
        if (category.value.id == -1) {
            category.value = await usePost<Category>('categories', serializeObject({
                ...category.value,
                thumbnail_file: thumbnailBlob.value
            }));
            history.replaceState(null, null, `/admin/categories/${category.value.id}`);
        } else {
            category.value = await usePatch<Category>(`categories/${category.value.id}`, serializeObject({
                ...category.value,
                thumbnail_file: thumbnailBlob.value
            }));
        }
        thumbnailBlob.value = null;
    } catch (error) {
        console.error(error);
        return;
    }
}
</script>