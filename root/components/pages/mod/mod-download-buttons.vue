<template>
    <flex>
        <a-button v-if="download && downloadType == 'file'" class="large-button flex-1" :to="!static ? downloadUrl : undefined">
            <i-mdi-download/> {{$t('download')}}
            <br>
            <span class="text-sm">{{(download as any).type}} - {{friendlySize((download as any).size)}}</span>
        </a-button>
        <VDropdown v-else-if="download && downloadType == 'link'" class="flex-1 flex">
            <a-button class="large-button flex-1" @click="!static && registerDownload(mod)">
                <i-mdi-download/> {{$t('show_download_link')}}
            </a-button>
            <template #popper>
                <div class="word-break p-2" style="width: 250px;">
                    {{$t('show_download_link_warn')}}
                    <br>
                    <a class="text-lg font-bold" :href="(download as any).url">{{(download as any).url}}</a>
                </div>
            </template>
        </VDropdown>
        <slot/>
    </flex>
</template>

<script setup lang="ts">
import type { Mod, File, Link } from '~~/types/models';
import { friendlySize } from '~~/utils/helpers';
import { registerDownload } from '~~/utils/mod-helpers';

const props = defineProps<{
    mod: Mod;
    download?: File|Link;
    static?: boolean;
}>();

const downloadType = computed(() => {
    if (props.mod.download_type) {
        return props.mod.download_type;
    } else if (props.download) {
        return Object.hasOwn(props.download, 'file') ? 'file' : 'link';
    }
});

const downloadUrl = computed(() => `/mod/${props.mod.id}/download/${props.download!.id}`);
</script>