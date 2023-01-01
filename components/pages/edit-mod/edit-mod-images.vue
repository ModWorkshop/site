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
            :url="uploadLink"
            :files="images"
            url-prefix="mods/images" 
            :max-files="settings.mod_max_image_count" 
            :max-file-size="settings.image_max_file_size" 
            max-size="50" 
            use-file-as-thumb 
            @file-uploaded="fileUploaded" 
            @file-deleted="fileDeleted"
        >
        <template #buttons="{file}">
            <a-button icon="image" :disabled="file.id == mod.thumbnail_id" @click.prevent="setThumbnail(file)">{{$t('thumbnail')}}</a-button>
            <a-button icon="image" :disabled="file.id == mod.banner_id" @click.prevent="setBanner(file)">{{$t('banner')}}</a-button>
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
const ignoreChanges: () => void = inject('ignoreChanges');

function setBanner(banner: Image) {
    props.mod.banner_id = banner && banner.id || null;
    props.mod.banner = banner;
}

function setThumbnail(thumb: Image) {
    props.mod.thumbnail_id = thumb && thumb.id || null;
    props.mod.thumbnail = thumb;
}

function fileUploaded(image: Image) {
    props.mod.images.push(image);
    //If we have changes already we don't want to ignore the changes
    //We ignore them since the changes are already "applied" due to files being instantly uploaded.
    ignoreChanges();
}

function fileDeleted(image: Image) {
    for (const [i, f] of Object.entries(props.mod.images)) {
        if (toRaw(f) === toRaw(image)) {
            props.mod.images.splice(parseInt(i), 1);
        }
    }
    
    if (props.mod.thumbnail_id === image.id) {
        setThumbnail(null);
    }

    if (props.mod.banner_id === image.id) {
        setBanner(null);
    }

    ignoreChanges();
}
</script>