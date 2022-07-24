<template>
    <div>
        <va-alert v-if="mod.legacy_banner_url" color="warning" class="mb-4">
            Banner URLs are being phased out. While old mods will still function, we expect new/updated mods
            to upload their banners to the site directly.
            <br>
            <strong class="text-lg">Saving the mod will remove the banner URL!</strong>
        </va-alert>
        <uploader name="images" :url="uploadLink" :files="images" url-prefix="mods/images/" use-file-as-thumb>
            <template #buttons="{file}">
                <a-button class="file-button cursor-pointer" icon="image" @click.prevent="mod.thumbnail_id = file.id">
                    Make Thumbnail
                </a-button>
                <a-button class="file-button cursor-pointer" icon="image" @click.prevent="mod.banner_id = file.id">
                    Make Banner
                </a-button>
            </template>
        </uploader>
    </div>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';
import clone from 'rfdc/default';

const props = defineProps<{
    mod: Mod
}>();

const uploadLink = computed(() => props.mod ? `mods/${props.mod.id}/images`: '');
const images = ref(clone(props.mod.images));
</script>