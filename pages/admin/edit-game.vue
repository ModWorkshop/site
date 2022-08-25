<template>
    <a-form :model="game" :can-save="canSaveOverride" :created="game.id != -1" float-save-gui @submit="save">
        <flex column gap="3">
            <img-uploader id="thumbnail" v-model="thumbnailBlob" label="Thumbnail" :src="game.thumbnail">
                <template #label="{ src }">
                    <game-thumbnail :src="src"/>
                </template>
            </img-uploader>
            <img-uploader id="banner" v-model="bannerBlob" label="Banner" :src="game.banner">
                <template #label="{ src }">
                    <a-banner :src="src" url-prefix="games/banners"/>
                </template>
            </img-uploader>
            <a-input v-model="game.name" label="Name"/>
            <a-input v-model="game.short_name" :label="$t('short_name')"/>
            <a-input v-model="game.buttons" :label="$t('game_buttons')"/>
            <a-input v-model="game.webhook_url" :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)"/>
            <div v-if="game.id">
                Categories
                <flex v-if="categories" column>
                    <category-tree :categories="categories.data"/>
                </flex>
            </div>
            <a-alert class="w-full" color="warning">
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
import { useStore } from "~~/store";
import { Category, Game } from "~~/types/models";

const store = useStore();

const categoryTemplate: Game = {
    id: -1,
    name: '',
    short_name: "",
    thumbnail: "",
    banner: "",
    buttons: "",
    webhook_url: "",
    last_date: "",
    created_at: "",
    updated_at: ""
};

let game: Ref<Game>;

await store.fetchGames();

const route = useRoute();
const thumbnailBlob = ref(null);
const bannerBlob = ref(null);
const canSaveOverride = computed(() => !!thumbnailBlob.value || !!bannerBlob.value);

if (route.params.gameId == 'new') {
    game = ref<Game>(categoryTemplate);
}
else if (route.params.gameId) {
    const { data, error } = await useFetchData<Game>(`http://localhost:8000/games/${route.params.gameId}`);
    game = data;

    useHandleError(error.value, {
        404: 'This game does not exist!'
    });
}

const { data: categories } = await useFetchMany<Category>(`http://localhost:8000/games/${game.value.id}/categories?include_paths=1`);

async function save() {
    try {
        if (game.value.id == -1) {
            game.value = await usePost<Game>('games', serializeObject({
                ...game.value,
                thumbnail_file: thumbnailBlob.value,
                banner_file: bannerBlob.value
            }));
            history.replaceState(null, null, `/admin/categories/${game.value.id}`);
        } else {
            game.value = await usePatch<Game>(`games/${game.value.id}`, serializeObject({
                ...game.value,
                thumbnail_file: thumbnailBlob.value,
                banner_file: bannerBlob.value
            }));
        }
    } catch (error) {
        console.error(error);
        return;
    }
}
</script>