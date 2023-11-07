<template>
    <flex column class="items-center text-center">
        <h2>{{$t('downloading_file')}}</h2>
        <h3>{{file.type}} - {{friendlySize(file.size)}}</h3>
        <h3>{{$t('downloading_file_should')}}</h3>
        <flex>
            <a-button :to="`/mod/${mod.id}`"><i-mdi-arrow-left/> {{$t('return_to_mod')}}</a-button>
            <a ref="download" download :href="file.download_url">
                <a-button><i-mdi-download/> {{$t('downloading_file_force')}}</a-button>
            </a>
            <a-button 
                v-if="mod.instructs_template || mod.instructions" 
                :to="`/mod/${mod.id}?tab=instructions`"
                color="warning"
            >
                <i-mdi-help/> {{$t('downloading_file_help')}}
            </a-button>
        </flex>
        <!-- <div class="mt-3" id="video_player" style="width: 640px; height: 360px;"/> -->
        <div id="mws-ads-video-ad"/>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { File, Mod } from '~~/types/models';
import { friendlySize } from '~~/utils/helpers';
import { registerDownload } from '~~/utils/mod-helpers';

const { t } = useI18n();

const { mod } = defineProps<{
    mod: Mod;
}>();

const download = ref<HTMLAnchorElement>();

const { data: file } = await useResource<File>('file', 'files');


if (!file.value) {
    throw createError({ statusCode: 404, statusMessage: t('file_doesnt_exist') });
}

//Annoyingly we needed to wrap the button in a different anchor element since ref doesn't always include the element in the DOM
//Basically if it's a componnent, it will be a component ref which doesn't seem to include the actual element!
//Otherwise if it's a simple element, it will be the element itself.
watch(download, () => {
    if (download.value) {
        download.value.click();
        registerDownload(mod);
    }
});

onMounted(() => {
    if (process.client) {
        // if (dataLayer) {
        //     console.log('Load video');
        //     dataLayer.push({event: "loadVideo"});
        // }
        window['nitroAds'].createAd('mws-ads-video-ad', {
            "format": "video-nc",
            "report": {
                "enabled": true,
                "icon": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            "video": {
                "float": "never"
            }
        });
    }
});
</script>