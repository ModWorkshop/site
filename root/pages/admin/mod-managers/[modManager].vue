<template>
    <simple-resource-form 
        v-model="manager"
        url="mod-managers"
        :game="game"
        :redirect-to="redirectTo"
        :can-save="canSaveOverride"
        :merge-params="mergeParams"
    >
        <!-- <img-uploader id="image" v-model="imageBlob" :label="$t('thumbnail')" :src="manager.image" url-prefix="mod-managers" width="200"/> -->
        <a-input v-model="manager.name" :label="$t('name')"/>
        <a-input v-model="manager.site_url" :label="$t('site_url')"/>
        <a-input v-model="manager.download_url" :label="$t('download_url')"/>
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