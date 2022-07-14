<template>
    <a-form @submit="save">
        <flex column gap="3">
            <div>
                <a-button icon="arrow-left" to="/admin/categories">Back to Categories</a-button>
            </div>
            <a-input label="Name" v-model="category.name"/>
            <a-input :label="$t('short_name')" v-model="category.short_name"/>
            <a-input :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)" v-model="category.webhook_url"/>
            <md-editor :label="$t('description')" v-model="category.desc"/>
            <flex>
                <a-select label="Game" v-model="category.game_id" placeholder="Select a game" clearable :options="games"/>
                <a-select label="Parent Category" v-model="category.parent_id" placeholder="Select a parent category" clearable :options="categories"/>
            </flex>
        </flex>
    </a-form>
</template>

<script setup lang="ts">
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

let category;

await store.fetchGames();

const categories = ref<Category[]>([]);
const games = store.games;
const route = useRoute();

if (route.params.id) {
    const { data } = await useAPIFetch<Category>(`categories/${route.params.id}`);
    category = data;
} else if(route.params.id == 'new') {
    category = ref<Category>(categoryTemplate);
}

watch(() => category.value.game_id, async () => {
    if (category.value.game_id) {
        const cats = await useGetMany<Category>(`/games/${category.game_id}/categories?include_paths=1`);
        categories.value = cats.data;
    } else {
        categories.value = [];
    }
}, {immediate: true});

async function save() {
    try {
        if (category.value.id == -1) {
            category.value = await usePatch<Category>(`categories/${category.value.id}`, category.value);
        } else {
            category.value = await usePost<Category>('categories', category.value);
        }
    } catch (error) {
        console.error(error);
        return;
    }
};
</script>