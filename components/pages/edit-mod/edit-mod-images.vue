<template>
    <a-alert v-if="mod.legacy_banner_url" color="warning" class="mb-4">
        <i18n-t keypath="banner_url_warning" tag="span">
            <template #url>
                <NuxtLink :href="mod.legacy_banner_url">{{mod.legacy_banner_url}}</NuxtLink>
            </template>
        </i18n-t>
    </a-alert>
    
    <label>{{$t('banner_preview')}}</label>
    <mod-banner class="w-full" :mod="mod" static/>

    <label>{{$t('thumbnail_preview')}}</label>
    <div style="width: 300px;">
        <a-mod :mod="mod" static/>
    </div>

    <label>{{$t('images')}}</label>
    <file-uploader 
            name="images"
            :upload-url="uploadLink"
            url="images"
            :files="images"
            url-prefix="mods/images" 
            :max-files="settings?.mod_max_image_count" 
            :max-file-size="settings?.image_max_file_size" 
            max-size="50" 
            use-file-as-thumb 
            @file-deleted="fileDeleted"
        >
        <template #buttons="{file}">
            <a-button icon="image" :disabled="file.id == mod.thumbnail_id" @click.prevent="setThumbnail(file as Image)">{{$t('thumbnail')}}</a-button>
            <a-button icon="image" :disabled="file.id == mod.banner_id" @click.prevent="setBanner(file as Image)">{{$t('banner')}}</a-button>
        </template>
    </file-uploader>
</template>

<script setup lang="ts">
import { Mod, Image } from '~~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~~/store';

const { settings } = useStore();

const props = defineProps<{
    mod: Mod,
    canSave?: boolean
}>();

const uploadLink = computed(() => props.mod ? `mods/${props.mod.id}/images`: '');
const images = ref(clone(props.mod.images));
const ignoreChanges: (() => void)|undefined = inject('ignoreChanges');

function setBanner(banner?: Image) {
    props.mod.banner_id = banner && banner.id || undefined;
    props.mod.banner = banner;
}

function setThumbnail(thumb?: Image) {
    props.mod.thumbnail_id = thumb && thumb.id || undefined;
    props.mod.thumbnail = thumb;
}

function fileDeleted(image: Image) {
    if (props.mod.thumbnail_id === image.id) {
        setThumbnail();
    }

    if (props.mod.banner_id === image.id) {
        setBanner();
    }

    ignoreChanges?.();
}
</script>