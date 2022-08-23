<template>
    <a-form :model="category" :created="!!category.id" float-save-gui @submit="save">
        <flex column gap="3">
            <div>
                <a-button icon="arrow-left" to="/admin/forum-categories">Back to Forum Categories</a-button>
            </div>
            <a-input v-model="category.name" required label="Name"/>
            <md-editor v-model="category.desc" :label="$t('description')"/>
            <a-select v-model="category.forum_id" label="Forum" placeholder="Select forum" required :options="forums.data"/>
            <a-alert v-if="category.id" class="w-full" color="warning">
                <details>
                    <summary>DANGER ZONE</summary>
                    <div class="p-4 mt-2">
                        <a-button color="danger">Delete</a-button>
                    </div>
                </details>
            </a-alert>
        </flex>
    </a-form>
</template>

<script setup lang="ts">
import { Ref } from "vue";
import { Forum, ForumCategory } from "~~/types/models";

const categoryTemplate: ForumCategory = {
    id: 0,
    name: '',
    forum_id: null,
    desc: "",
    created_at: "",
    updated_at: ""
};

let category: Ref<ForumCategory>;

const route = useRoute();

const { data: forums } = await useFetchMany<Forum>('forums');

if (route.params.id == 'new') {
    category = ref<ForumCategory>(categoryTemplate);
}
else if (route.params.id) {
    const { data } = await useFetchData<ForumCategory>(`forum-categories/${route.params.id}`);
    category = data;
}

async function save() {
    try {
        if (category.value.id) {
            category.value = await usePatch<ForumCategory>(`forum-categories/${category.value.id}`, category.value);
        } else {
            category.value = await usePost<ForumCategory>('forum-categories', category.value);
            history.replaceState(null, null, `/admin/categories/${category.value.id}`);
        }
    } catch (error) {
        console.error(error);
        return;
    }
}
</script>