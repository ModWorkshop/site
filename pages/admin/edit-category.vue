<template>
    <simple-resource-form v-model="category" url="categories" :redirect-to="`/admin/games/${gameId}/categories`">
        <a-input v-model="category.name" label="Name"/>
        <a-input v-model="category.webhook_url" :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)"/>
        <a-input v-model="category.approval_only" :label="$t('approval_only')" type="checkbox" desc="Whether or not mods uploaded to this category need to first be approved by a moderator"/>
        <md-editor v-model="category.desc" :label="$t('description')"/>
        <flex v-if="categories" column gap="2">
            <label>Category</label>
            <category-tree v-model="category.parent_id" style="height: 200px;" class="input p-2 overflow-y-scroll" :categories="validCategories"/>
        </flex>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Category } from "~~/types/models";

const route = useRoute();
const gameId = route.params.gameId;

const { data: category } = await useEditResource('category', 'categories', {
    id: -1,
    name: '',
    game_id: gameId,
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
});

const { data: categories } = await useFetchMany<Category>(`games/${gameId}/categories`);

const validCategories = computed(() => categories.value.data.filter(cat => {
    //Don't include the category itself
    if (cat.id === category.value.id) {
        return false;
    }

    //Don't include any child categories to avoid circular reference
    let current = categories.value.data[cat.parent_id];
    while(current) {
        if (current.parent_id == category.value.parent_id) {
            return false;
        }
    }

    return true;
}));
</script>