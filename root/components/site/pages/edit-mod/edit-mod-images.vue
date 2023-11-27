<template>
    <m-alert v-if="mod.legacy_banner_url" color="warning" class="mb-4">
        <i18n-t keypath="banner_url_warning" tag="span" scope="global">
            <template #url>
                <NuxtLink :href="mod.legacy_banner_url">{{mod.legacy_banner_url}}</NuxtLink>
            </template>
        </i18n-t>
    </m-alert>
    
    <m-alert :desc="$t('images_help')"/>

    <label>{{$t('images')}}</label>
    <m-file-uploader 
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
            <m-button :disabled="!file.id || file.id == mod.thumbnail_id" @click.prevent="setThumbnail(file as Image)">
                <i-mdi-image-outline/> {{$t('thumbnail')}}
            </m-button>
            <m-button :disabled="!file.id || file.id == mod.banner_id" @click.prevent="setBanner(file as Image)">
                <i-mdi-image-outline/> {{$t('banner')}}
            </m-button>
        </template>
    </m-file-uploader>
    <m-flex class="mr-auto">
        <m-button @click="setBanner()">
            <i_mdi-close/> {{ $t('reset_banner') }}
        </m-button>
        <m-button @click="setThumbnail()">
            <i_mdi-close/> {{ $t('reset_thumbnail') }}
        </m-button>
    </m-flex>
    <label>{{$t('thumbnail_preview')}}</label>
    <div class="alt-content-bg p-4">
        <div style="max-width: 300px;">
            <grid-mod :mod="mod" static/>
        </div>
    </div>

    <label>{{$t('banner_preview')}}</label>
    <mod-banner class="w-full" :mod="mod" static/>
</template>

<script setup lang="ts">
import type { Mod, Image } from '~~/types/models';
import { useStore } from '~~/store';

const { settings } = useStore();

const mod = defineModel<Mod>({ required: true });
const uploadLink = computed(() => mod.value ? `mods/${mod.value.id}/images`: '');
const images = ref(mod.value.images);

function setBanner(banner?: Image) {
    mod.value.banner_id = banner && banner.id || undefined;
    mod.value.banner = banner;
}

function setThumbnail(thumb?: Image) {
    mod.value.thumbnail_id = thumb && thumb.id || undefined;
    mod.value.thumbnail = thumb;
}

function fileDeleted(image: Image) {
    if (mod.value.thumbnail_id === image.id) {
        setThumbnail();
    }

    if (mod.value.banner_id === image.id) {
        setBanner();
    }
}
</script>