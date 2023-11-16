<template>
    <simple-resource-form 
        v-model="vmGame"
        :can-save="canSaveOverride"
        :merge-params="mergeParams"
        url="games"
        redirect-to="/g"
        :delete-button="canDelete"
        @submit="submit"
        >
        <img-uploader id="thumbnail" v-model="thumbnailBlob" :label="$t('thumbnail')" :src="vmGame.thumbnail">
            <template #label="{ src }">
                <game-thumbnail :src="src" style="width: 250px;"/>
            </template>
        </img-uploader>
        <img-uploader id="banner" v-model="bannerBlob" :label="$t('banner')" :src="vmGame.banner">
            <template #label="{ src }">
                <a-banner :src="src" url-prefix="games/images"/>
            </template>
        </img-uploader>
        <flex>
            <a-input v-model="vmGame.name" :label="$t('name')"/>
            <a-input v-model="vmGame.short_name" :label="$t('short_name')"/>
        </flex>
        <a-input v-model="vmGame.buttons" :label="$t('game_buttons')" :desc="$t('game_buttons_desc')"/>
        <a-input v-model="vmGame.webhook_url" :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)"/>
        <flex class="items-center">
            <a-select v-model="vmGame.default_mod_manager_id" :options="modManagers?.data" :label="$t('default_mod_manager')" :desc="$t('default_mod_manager_desc')"/>
            <a-select v-model="vmGame.mod_manager_ids" :options="modManagers?.data" multiple :label="$t('applied_global_mod_managers')" :desc="$t('applied_global_mod_managers_desc')"/>
        </flex>
    </simple-resource-form>
</template>

<script setup lang="ts">
import type { Game, ModManager } from '~~/types/models';
import { useStore } from '../../../store/index';

const thumbnailBlob = ref();
const bannerBlob = ref();
const canSaveOverride = computed(() => !!(thumbnailBlob.value || bannerBlob.value));
const { hasPermission } = useStore();

const props = defineProps<{
    game: Game
}>();

const vmGame = useVModel(props, 'game');
const canDelete = computed(() => hasPermission('manage-games'));

const { data: modManagers } = await useFetchMany<ModManager>('mod-managers', {
    params: {
        global: true
    }
});

const mergeParams = reactive({
    thumbnail_file: thumbnailBlob,
    banner_file: bannerBlob
});

function submit() {    
    thumbnailBlob.value = null;
    bannerBlob.value = null;
}
</script>