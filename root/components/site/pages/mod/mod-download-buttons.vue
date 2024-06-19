<template>
    <m-flex>
        <m-button v-if="download && type == 'file'" class="large-button flex-1" :to="!static ? downloadUrl : undefined">
            <i-mdi-download/> {{$t('download')}}
            <br>
            <span class="text-sm">{{(download as any).type}} - {{friendlySize((download as any).size)}}</span>
        </m-button>
        <m-dropdown v-else-if="download && type == 'link'" class="flex-1 flex">
            <m-button class="large-button flex-1" @click="!static && registerDownload(mod)">
                <i-mdi-download/> {{$t('show_download_link')}}
            </m-button>
            <template #content>
                <div class="word-break p-2" style="width: 250px;">
                    {{$t('show_download_link_warn')}}
                    <br>
                    <a class="text-lg font-bold" :href="(download as any).url">{{(download as any).url}}</a>
                </div>
            </template>
        </m-dropdown>
        <slot/>
    </m-flex>
    <m-flex v-if="primaryModManager && download && type == 'file'">
        <m-dropdown>
            <m-button class="large-button text-center h-full">
                <i-mdi-chevron-down/>
            </m-button>
            <template #content>
                <m-dropdown-item v-for="manager of mod.mod_managers" :key="manager.id" @click="() => setModManager(manager)">{{ manager.name }}</m-dropdown-item>
            </template>
        </m-dropdown>

        <m-button class="large-button" style="flex: 6;" :to="!static ? getManagerDownloadUrl(primaryModManager, download as File) : undefined">
            <template #container>
                <m-flex class="w-full text-center">
                    <span class="flex-1"><i-mdi:cog-box/> {{ primaryModManager.name }} <br> <small>{{$t('mod_manager_install')}}</small></span>
                </m-flex>
            </template>
        </m-button>
    </m-flex>
</template>

<script setup lang="ts">
import type { Mod, ModManager, File, Link } from '~~/types/models';
import { friendlySize } from '~~/utils/helpers';
import { registerDownload } from '~~/utils/mod-helpers';

const props = defineProps<{
    mod: Mod;
    download?: File|Link;
    static?: boolean;
    type?: 'link'|'file';
}>();

const chosenModManager = useCookie<number>(props.mod.game_id + '-mod-manager', { decode: parseInt, expires: longExpiration() });

const managers = computed(() => props.mod.mod_managers ?? []);


const downloadUrl = computed(() => `/mod/${props.mod.id}/download/${props.download!.id}`);

const primaryModManager = computed(() => {
    if (props.mod.disable_mod_managers || props.mod.category?.disable_mod_managers) {
        return null;
    }

    const chosen = chosenModManager.value;
    const defaultManager = props.mod.game?.default_mod_manager_id;

    let manager: ModManager|undefined = managers.value[0];
        manager = managers.value?.find(manager => manager.id == defaultManager || manager.id == chosen) ?? manager;

    return manager;
});

function setModManager(manager: ModManager) {
    chosenModManager.value = manager.id;
}

function getManagerDownloadUrl(manager: ModManager, file: File) {
    const replace = {
        ':mod_id': props.mod.id,
        ':file_id': file.id,
        ':manager_name': manager.name,
        ':game_id': props.mod.game?.id,
    };
    return manager.download_url.replaceAll(/:\w+_?\w*/g, (str) => replace[str]);
}
</script>