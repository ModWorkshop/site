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
    <flex v-if="primaryModManager && download && downloadType == 'file'">
        <VDropdown>
            <a-button class="large-button text-center h-full">
                <i-mdi-chevron-down/>
            </a-button>
            <template #popper>
                <a-dropdown-item v-for="manager of mod.mod_managers" :key="manager.id" @click="() => setModManager(manager)">{{ manager.name }}</a-dropdown-item>
            </template>
        </VDropdown>

        <a-button class="large-button" style="flex: 6;" :to="!static ? getManagerDownloadUrl(primaryModManager, download as File) : undefined">
            <template #container>
                <flex class="w-full text-center">
                    <span class="flex-1"><i-mdi:cog-box/> {{ primaryModManager.name }} <br> <small>{{$t('mod_manager_install')}}</small></span>
                </flex>
            </template>
        </a-button>
    </flex>
</template>

<script setup lang="ts">
import type { Mod, ModManager, File, Link } from '~~/types/models';
import { friendlySize } from '~~/utils/helpers';
import { registerDownload } from '~~/utils/mod-helpers';

const props = defineProps<{
    mod: Mod;
    download?: File|Link;
    static?: boolean;
}>();

const chosenModManager = useCookie<number>(props.mod.game_id + '-mod-manager', { decode: parseInt, expires: longExpiration() });

const managers = computed(() => props.mod.mod_managers ?? []);
const downloadType = computed(() => {
    if (props.mod.download_type) {
        return props.mod.download_type;
    } else if (props.download) {
        return Object.hasOwn(props.download, 'file') ? 'file' : 'link';
    }
});

const downloadUrl = computed(() => `/mod/${props.mod.id}/download/${props.download!.id}`);

const primaryModManager = computed(() => {
    if (props.mod.disable_mod_managers || props.mod.category?.disable_mod_managers) {
        return null;
    }

    const chosen = chosenModManager.value;
    const defaultManager = props.mod.game?.default_mod_manager_id;

    let manager: ModManager|undefined = managers.value[0];
    if (chosen) {
        manager = managers.value?.find(manager => manager.id == defaultManager || manager.id == chosen) ?? manager;
    }

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