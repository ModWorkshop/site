<template>
    <a-form :model="game" :can-save="canSaveOverride" :created="game.id != -1" float-save-gui @submit="save">
        <flex column gap="3">
            <div>
                <a-button icon="arrow-left" to="/admin/games">Back to Games</a-button>
            </div>
            <img-uploader id="thumbnail" label="Thumbnail" :src="(game.thumbnail && `games/thumbnails/${game.thumbnail}`) || 'assets/nopreview.webp'" v-model="thumbnailBlob">
                <template #label="{ src }">
                    <a-img class="round" :src="src"/>
                </template>
            </img-uploader>
            <img-uploader id="banner" label="Banner" :src="(game.banner && `games/banners/${game.banner}`) || 'banners/default_banner.webp'" v-model="bannerBlob">
                <template #label="{ src }">
                    <div :class="{'banner': true, 'p-2': true, round: true, 'default-banner': !src}" :style="{backgroundImage: `url(${src})`}"/>
                </template>
            </img-uploader>
            <a-input v-model="game.name" label="Name"/>
            <a-input v-model="game.short_name" :label="$t('short_name')"/>
            <a-input v-model="game.buttons" :label="$t('game_buttons')"/>
            <a-input :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)" v-model="game.webhook_url"/>
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
import { Game } from "~~/types/models";

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

if (route.params.id == 'new') {
    game = ref<Game>(categoryTemplate);
}
else if (route.params.id) {
    const { data } = await useFetchData<Game>(`games/${route.params.id}`);
    game = data;
}

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