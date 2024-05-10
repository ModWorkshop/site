<template>
    <m-alert v-if="mod.legacy_banner_url" color="warning" class="mb-4">
        <i18n-t keypath="banner_url_warning" tag="span" scope="global">
            <template #url>
                <NuxtLink :href="mod.legacy_banner_url">{{mod.legacy_banner_url}}</NuxtLink>
            </template>
        </i18n-t>
    </m-alert>
    
    <m-alert :desc="$t('images_help')"/>
    
    <m-flex v-if="mod.id" class="items-center">
        <m-select v-model="mod.thumbnail_id" :options="images" :label="$t('Thumbnail')" :filterable="false" clearable null-clear height="100px">
            <template #any-option="{ option }">
                <a-thumbnail url-prefix="mods/images" :src="option.file" style="height: 100px;"/>
            </template>
        </m-select>
        <m-select v-model="mod.banner_id" :options="images" :label="$t('Banner')" :filterable="false" clearable null-clear height="100px">
            <template #any-option="{ option }">
                <a-thumbnail url-prefix="mods/images" :src="option.file" style="height: 100px;"/>
            </template>
        </m-select>
        <m-select v-model="mod.background_id" :disabled="!mod.user?.has_supporter_perks" :options="images" :label="true" :filterable="false" clearable null-clear height="100px">
            <template #any-option="{ option }">
                <a-thumbnail url-prefix="mods/images" :src="option.file" style="height: 100px;"/>
            </template>
            <template #label>
                {{ $t('supporter_background') }} <NuxtLink to="/support">{{$t('supporters_only')}}</NuxtLink>
            </template>
        </m-select>
    </m-flex>
    <m-input 
        v-if="mod.user?.has_supporter_perks"
        v-model="mod.background_opacity"
        :label="true"
        type="range"
        step="0.01"
        min="0"
        max="1"
    >
        <template #label>
            {{ $t('supporter_background_opacity') }} <NuxtLink to="/support">{{$t('supporters_only')}}</NuxtLink>
        </template>
    </m-input>

    <m-file-uploader 
        v-model="images"
        name="images"
        :upload-url="uploadLink"
        url="images"
        url-prefix="mods/images"
        :max-files="settings?.mod_max_image_count"
        :max-file-size="settings?.image_max_file_size"
        :paused="!mod.id"
        :max-size="50 * 1024e3"
        use-file-as-thumb
        @file-deleted="fileDeleted"
        @update:model-value="fileUploaded"
    >
        <template #buttons="{file}">
            <m-flex>
                <m-button :disabled="!file.id || file.id == mod.thumbnail_id" @click.prevent="setThumbnail(file as Image)">
                    <i-mdi-image-outline/> {{$t('thumbnail')}}
                </m-button>
                <m-button :disabled="!file.id || file.id == mod.banner_id" @click.prevent="setBanner(file as Image)">
                    <i-mdi-image-outline/> {{$t('banner')}}
                </m-button>
            </m-flex>
            <m-flex>
                <m-button class="grow" :disabled="!file.id" @click.prevent="setImageOrder(file as Image, -1)">
                    <i-mdi-arrow-left-drop-circle/>
                </m-button>
                <m-button class="grow" :disabled="!file.id" @click.prevent="setImageOrder(file as Image, 1)">
                    <i-mdi-arrow-right-drop-circle/>
                </m-button>
            </m-flex>
        </template>
    </m-file-uploader>
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
import clone from 'rfdc/default';

const { settings } = useStore();
const showError = useQuickErrorToast();

const mod = defineModel<Mod>({ required: true });

const uploadLink = computed(() => mod.value ? `mods/${mod.value.id}/images`: '');

const images = ref<Image[]>(clone(mod.value.images) ?? []);

function setBanner(banner?: Image) {
    mod.value.banner_id = banner && banner.id || null;
    mod.value.banner = banner;
}

function setThumbnail(thumb?: Image) {
    mod.value.thumbnail_id = thumb && thumb.id || null;
    mod.value.thumbnail = thumb;
}

async function setImageOrder(img: Image, order: number) {
    try {
        await patchRequest(`images/${img.id}`, { display_order: img.display_order + order });
        img.display_order = img.display_order + order;

        images.value = images.value.filter(v => v != img);
        images.value.splice(img.display_order, 0, img);

        for (let i = 0; i < images.value.length; i++) {
            images.value[i].display_order = i;            
        }
    } catch (error) {
        showError(error);
    }
}

function fileDeleted(image: Image) {
    if (mod.value.thumbnail_id === image.id) {
        setThumbnail();
    }

    if (mod.value.banner_id === image.id) {
        setBanner();
    }

    for (let i = 0; i < images.value.length; i++) {
        images.value[i].display_order = i;            
    }
}

function fileUploaded() {
    console.log(images.value);
    
    for (let i = 0; i < images.value.length; i++) {
        images.value[i].display_order = i;            
    }
}
</script>