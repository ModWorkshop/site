<template>
    <simple-resource-form v-model="category" url="forum-categories" redirect-to="/admin/forum-categories">
        <a-input v-model="category.name" required label="Name"/>
        <md-editor v-model="category.desc" :label="$t('description')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { ForumCategory, Game } from "~~/types/models";

let forumId = 1;

const route = useRoute();
if (route.params.gameId) {
    const { data: game } = await useFetchData<Game>(`games/${route.params.gameId}`);
    forumId = game.value.forum_id;
}

const { data: category } = await useEditResource<ForumCategory>('category', 'forum-categories', {
    id: 0,
    name: '',
    forum_id: forumId,
    desc: "",
    created_at: "",
    updated_at: ""
});
</script>