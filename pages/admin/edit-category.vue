<template>
    <simple-resource-form v-model="category" url="categories" :redirect-to="`/admin/games/${gameId}/categories`">
        <a-input v-model="category.name" label="Name"/>
        <a-input v-model="category.webhook_url" :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)"/>
        <a-input v-model="category.approval_only" :label="$t('approval_only')" type="checkbox" desc="Whether or not mods uploaded to this category need to first be approved by a moderator"/>
        <md-editor v-model="category.desc" :label="$t('description')"/>
        <a-select v-model="category.parent_id" label="Parent Category" placeholder="Select a parent category" clearable :options="categories"/>
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

const categories = await useFetchMany<Category>(`games/${gameId}/categories`);
</script>