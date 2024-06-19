<template>
    <simple-resource-form 
        v-model="manager"
        url="mod-managers"
        :game="game"
        :redirect-to="redirectTo"
        :can-save="canSaveOverride"
        :merge-params="mergeParams"
    >
        <!-- <m-img-uploader id="image" v-model="imageBlob" :label="$t('thumbnail')" :src="manager.image" url-prefix="mod-managers" width="200"/> -->
        <m-input v-model="manager.name" :label="$t('name')"/>
        <m-input v-model="manager.site_url" :label="$t('manager_site_url')" :desc="$t('manager_site_url_desc')"/>
        <m-input v-model="manager.download_url" :label="$t('manager_download_url')" :desc="$t('manager_download_url_desc')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import type { ModManager, Game } from '~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-tags', props.game);

const redirectTo = computed(() => getAdminUrl('mod-managers', props.game));

const imageBlob = ref();
const canSaveOverride = computed(() => !!imageBlob.value);

const { data: manager } = await useEditResource<ModManager>('modManager', 'mod-managers', {
    id: 0,
    name: '',
    site_url: '',
    download_url: '',
});

//Unused atm
const mergeParams = reactive({
    image_file: imageBlob,
});
</script>