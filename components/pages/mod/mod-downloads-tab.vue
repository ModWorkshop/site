<template>
    <div v-for="labeled of labeledDownloads" :key="labeled.label" class="flex-grow">
        <h2 v-if="labeled.label != 'all'">{{labeled.label}}</h2>
        <flex column gap="2">
            <flex v-for="file of labeled.downloads" :key="file.id" gap="3" wrap class="alt-content-bg p-3 items-center place-content-center">
                <div class="mr-2">
                    <a-img src="https://modworkshop.net/mydownloads/previews/fileimages/file_dl_big.png" width="128" height="128"/>
                </div>
                <flex grow column>
                    <h3 v-if="file.name">{{file.name}}</h3>
                    <h3 v-else>{{$t(`file_type_${file.download_type}`)}}</h3>
                    <span v-if="file.version">
                        <a-icon icon="tag" :title="$t('version')"/> {{file.version}}
                    </span>
                    <flex class="items-center">
                        <a-icon icon="clock" :title="$t('upload_date')"/>
                        <i18n-t keypath="by_user_time_ago">
                            <template #time>
                                <time-ago :time="file.created_at"/>
                            </template>
                            <template #user>
                                <a-user :user="file.user" avatar-size="xs"/>
                            </template>
                        </i18n-t>
                    </flex>
                    <a-markdown v-if="file.desc" class="mt-3" :text="file.desc"/>
                </flex>
                <div>
                    <a-button v-if="file.download_type == 'file' && (file as File).size" class="text-xl text-center" :to="`${modUrl}/download/${file.id}`" icon="mdi:download">
                        {{$t('download')}}
                        <small class="mt-2 text-center block">{{(file as File).type}} - {{friendlySize((file as File).size)}}</small>
                    </a-button>
                    <VDropdown v-else>
                        <a-button class="large-button w-full text-center" icon="mdi:download">
                            {{$t('show_download_link')}}
                        </a-button>
                        <template #popper>
                            <div class="p-2">
                                {{$t('show_download_link_warn')}}
                                <br>
                                <a :href="file.url">{{file.url}}</a>
                            </div>
                        </template>
                    </VDropdown>
                </div>
            </flex>
        </flex>
    </div>
</template>

<script setup lang="ts">
import { Download, File, Link, Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const modUrl = computed(() => `/mod/${props.mod.id}`);

const labeledDownloads = computed(() => {
    const sorted: { 
        label: string,
        downloads: Download[]
    }[] = [];

    function collectDownloads(downloads: (File|Link)[], download_type: 'file'|'link') {
        for (const file of downloads) {
            const label = file.label ?? 'all';
            let labeled = sorted.find(curr => curr.label == label);
    
            if (!labeled) {
                labeled = { label, downloads: [] };
                sorted.push(labeled);
            }
    
            labeled.downloads.push({
                download_type,
                ...file
            });
        }
    }

    if (props.mod.files) {
        collectDownloads(props.mod.files, 'file');
    }
    if (props.mod.links) {
        collectDownloads(props.mod.links, 'link');
    }
    
    return sorted;
});

</script>