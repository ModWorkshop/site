<template>
    <simple-resource-form v-model="game" :can-save="canSaveOverride" :merge-params="mergeParams" url="games" redirect-to="/admin/games" @submit="submit">
        <img-uploader id="thumbnail" v-model="thumbnailBlob" :label="$t('thumbnail')" :src="game.thumbnail">
            <template #label="{ src }">
                <game-thumbnail :src="src" style="height: 250px;"/>
            </template>
        </img-uploader>
        <img-uploader id="banner" v-model="bannerBlob" :label="$t('banner')" :src="game.banner">
            <template #label="{ src }">
                <a-banner :src="src" url-prefix="games/banners"/>
            </template>
        </img-uploader>
        <a-input v-model="game.name" :label="$t('name')"/>
        <a-input v-model="game.short_name" :label="$t('short_name')"/>
        <a-input v-model="game.buttons" :label="$t('game_buttons')"/>
        <a-input v-model="game.webhook_url" :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const thumbnailBlob = ref(null);
const bannerBlob = ref(null);
const canSaveOverride = computed(() => !!(thumbnailBlob.value || bannerBlob.value));

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-game', props.game);

const mergeParams = reactive({
    thumbnail_file: thumbnailBlob,
    banner_file: bannerBlob
});

function submit() {    
    thumbnailBlob.value = null;
    bannerBlob.value = null;
}
</script>