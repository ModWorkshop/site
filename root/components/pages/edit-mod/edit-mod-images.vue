<template>
    <a-alert v-if="mod.legacy_banner_url" color="warning" class="mb-4">
        <i18n-t keypath="banner_url_warning" tag="span" scope="global">
            <template #url>
                <NuxtLink :href="mod.legacy_banner_url">{{mod.legacy_banner_url}}</NuxtLink>
            </template>
        </i18n-t>
    </a-alert>
    
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
            <a-button icon="image" :disabled="!file.id || file.id == mod.thumbnail_id" @click.prevent="setThumbnail(file as Image)">{{$t('thumbnail')}}</a-button>
            <a-button icon="image" :disabled="!file.id || file.id == mod.banner_id" @click.prevent="setBanner(file as Image)">{{$t('banner')}}</a-button>
        </template>
    </file-uploader>
    <flex class="mr-auto">
        <a-button icon="mdi:close" @click="setBanner(null)">{{ $t('reset_banner') }}</a-button>
        <a-button icon="mdi:close" @click="setThumbnail(null)">{{ $t('reset_thumbnail') }}</a-button>
    </flex>
    <label>{{$t('thumbnail_preview')}}</label>
    <div class="alt-content-bg p-4">
        <div style="width: 300px;">
            <a-mod :mod="mod" static/>
        </div>
    </div>

    <label>{{$t('banner_preview')}}</label>
    <mod-banner class="w-full" :mod="mod" static/>
</template>

<script setup lang="ts">
import { Mod, Image } from '~~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~~/store';

const { settings } = useStore();

const props = defineProps<{
    mod: Mod,
}>();

const uploadLink = computed(() => props.mod ? `mods/${props.mod.id}/images`: '');
const images = ref(clone(props.mod.images));
const ignoreChanges: (() => void)|undefined = inject('ignoreChanges');

function setBanner(banner?: Image) {
    props.mod.banner_id = banner && banner.id || null;
    props.mod.banner = banner;
}

function setThumbnail(thumb?: Image) {
    props.mod.thumbnail_id = thumb && thumb.id || null;
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