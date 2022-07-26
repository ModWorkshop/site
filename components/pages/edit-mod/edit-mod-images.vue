<template>
    <flex column gap="4">
        <va-alert v-if="mod.legacy_banner_url" color="warning" class="mb-4">
            Banner URLs are being phased out. While old mods will still function, we expect new/updated mods
            to upload their banners to the site directly.
            <br>
            <strong class="text-lg">Saving the mod will remove the banner URL!</strong>
        </va-alert>

        <label>Banner Preview</label>
        <mod-banner class="w-full" :mod="mod" static/>

        <label>Thumbnail/Mod Card Preview</label>
        <div style="width: 300px;">
            <a-mod :mod="mod" static/>
        </div>

        <uploader name="images" :url="uploadLink" :files="images" url-prefix="mods/images/" use-file-as-thumb @file-uploaded="fileUploaded" @file-deleted="fileDeleted">
            <template #buttons="{file}">
                <a-button class="file-button cursor-pointer" icon="image" @click.prevent="setThumbnail(file)">
                    Make Thumbnail
                </a-button>
                <a-button class="file-button cursor-pointer" icon="image" @click.prevent="setBanner(file)">
                    Make Banner
                </a-button>
            </template>
        </uploader>
    </flex>
</template>

<script setup lang="ts">
import { Mod, Image } from '~~/types/models';
import clone from 'rfdc/default';

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
    if (!props.canSave) {
        ignoreChanges();
    }
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

    if (!props.canSave) {
        ignoreChanges();
    }
}
</script>