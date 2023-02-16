<template>
    <simple-resource-form v-if="category" v-model="category" :url="url" :redirect-to="`/admin/${url}`">
        <a-input v-model="category.name" :label="$t('name')"/>
        <a-input v-model="category.webhook_url" :label="$t('webhook_url')" :desc="$t('webhook_url_desc')"/>
        <a-input v-model="category.approval_only" :label="$t('approval_only')" type="checkbox" :desc="$t('approval_only_desc')"/>
        <md-editor v-model="category.desc" :label="$t('description')"/>
        <flex v-if="categories" column gap="2">
            <label>{{$t("parent_category")}}</label>
            <category-tree v-model="category.parent_id" style="height: 200px;" class="input p-2 overflow-y-scroll" :categories="validCategories"/>
        </flex>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Category, Game } from "~~/types/models";

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-categories', props.game);

const route = useRoute();
const gameId = route.params.game;
const url = getGameResourceUrl('categories', props.game);

const { data: category } = await useEditResource('category', 'categories', {
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

const validCategories = computed(() => categories.value?.data.filter(cat => {
    //Don't include the category itself
    if (cat.id === category.value!.id) {
        return false;
    }

    if (!cat.parent_id) {
        return true;
    }

    //Don't include any child categories to avoid circular reference
    const current = categories.value!.data[cat.parent_id];
    while(current) {
        if (current.parent_id == category.value!.parent_id) {
            return false;
        }
    }

    return true;
}) || []);
</script>