<template>
    <m-flex class="w-full mb-4">
        <span class="h2">{{ $t('downloads') }}</span>
        <m-button class="ml-auto" click="setPrimaryDownload()"><i-mdi-close/> {{ $t('clear_primary_download') }}</m-button>
    </m-flex>

    <label>{{$t('files')}}</label>
    <m-flex column>
        <small>{{$t('allowed_size_per_mod', [friendlySize(maxSize)])}}</small>
        <m-progress :percent="usedSizePercent" :text="usedSizeText" :color="fileSizeColor"/>
        <m-file-uploader
            v-model="files"
            list
            name="files"
            :upload-url="uploadLink"
            max-files="25"
            :paused="!mod.id"
            :max-size="(settings?.max_file_size || 0)"
            url="files"
            @file-uploaded="updateHasDownload"
            @file-deleted="fileDeleted"
        >
            <template #headers>
                <th class="text-center">{{$t('primary')}}</th>
            </template>
            <template #rows="{file}">
                <td class="text-center">
                    <input 
                        :checked="(file.id === mod.download_id && mod.download_type == 'file') ? true : undefined"
                        type="radio"
                        :disabled="file.waiting || !!file.progress"
                        @change="setPrimaryDownload('file', file as File)">
                </td>
            </template>
            <template #buttons="{file}">
                <m-button class="file-button" :disabled="file.waiting || !!file.progress" @click.prevent="editFile(file as File)">
                    <i-mdi-cog/>
                </m-button>
            </template>
        </m-file-uploader>
    </m-flex>

    <m-flex column>
        <m-flex class="items-center">
            <label>{{$t('links')}}</label>
            <m-button v-if="links && links.length < 25" class="ml-auto" @click="createNewLink">
                <i-mdi-plus-thick/>
            </m-button>
        </m-flex>
        <m-table alt-background>
            <template #head>
                <th>{{$t('name')}}</th>
                <th>{{$t('url')}}</th>
                <th>{{$t('date')}}</th>
                <th class="text-center">{{$t('actions')}}</th>
                <th class="text-center">{{$t('primary')}}</th>
            </template>
            <template #body>
                <template v-if="links.length">
                    <tr v-for="link of links" :key="link.id">
                        <td>{{link.name}}</td>
                        <td>{{link.url}}</td>
                        <td v-if="link.id">{{fullDate(link.updated_at)}}</td>
                        <td v-else>{{$t('waiting_for_mod')}}</td>
                        <td class="text-center p-1">
                            <m-flex inline>
                                <m-button @click.prevent="editLink(link)"><i-mdi-cog/></m-button>
                                <m-button @click.prevent="deleteLink(link)"><i-mdi-delete/></m-button>
                            </m-flex>
                        </td>
                        <td class="text-center">
                            <input :checked="(link.id === mod.download_id && mod.download_type == 'link') ? true : undefined" type="radio" @change="setPrimaryDownload('link', link)">
                        </td>
                    </tr>
                </template>
                <tr v-else>
                    <td colspan="5" class="text-center">
                        {{$t('nothing_found')}}
                    </td>
                </tr>
            </template>
        </m-table>
    </m-flex>

    <span class="h2 my-4">{{ $t('updates') }}</span>

    <m-input v-if="!light" v-model="mod.version" :label="$t('version')"/>
    <md-editor v-if="!light" v-model="mod.changelog" :label="$t('changelog')" rows="12"/>
    <m-input v-if="!light" v-model="mod.disable_mod_managers" :label="$t('disable_mod_managers')" :desc="$t('disable_mod_managers_desc')" type="checkbox"/>

    <m-input v-if="canModerate && !light" v-model="mod.allowed_storage" type="number" max="1000" :label="$t('allowed_storage')" :desc="$t('allowed_storage_help')"/>
    <m-form-modal v-if="currentLink" v-model="showEditLink" :title="$t('edit_link')" @submit="saveEditLink">
        <m-input v-model="currentLink.name" required :label="$t('name')"/>
        <m-input v-model="currentLink.label" :label="$t('label')"/>
        <m-input v-model="currentLink.url" type="url" required :label="$t('url')"/>
        <m-input v-model="currentLink.version" :label="$t('version')"/>
        <m-select v-model="currentLink.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable>
            <template #any-option="{ option }">
                <m-img style="width: 150px; height: 150px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
            </template>
        </m-select>
        <md-editor v-model="currentLink.desc" rows="8" :label="$t('description')"/>
    </m-form-modal>
    <m-form-modal v-if="currentFile" v-model="showEditFile" :title="$t('edit_file')" @submit="saveEditFile">
        <m-input v-model="currentFile.name" :label="$t('name')"/>
        <m-input v-model="currentFile.label" :label="$t('label')"/>
        <m-input v-model="currentFile.version" :label="$t('version')"/>
        <m-input v-model:elementRef="changeFile" type="file" :label="$t('upload_file')"/>
        <m-select v-model="currentFile.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable>
            <template #any-option="{ option }">
                <m-img style="width: 150px; height: 150px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
            </template>
        </m-select>
        <md-editor v-model="currentFile.desc" rows="8" :label="$t('description')"/>
    </m-form-modal>
</template>

<script setup lang="ts">
import type { File, Link, Mod } from '~~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~~/store';
import { friendlySize, fullDate } from '~~/utils/helpers';

const { settings, hasPermission, user } = useStore();

defineProps<{
    light?: boolean
}>();

const mod = defineModel<Mod>({ required: true });

const { data: asyncFiles } = await useWatchedFetchMany(`mods/${mod.value.id}/files`, { limit: 25 }, { immediate: !!mod.value.id });
const { data: asyncLinks } = await useWatchedFetchMany(`mods/${mod.value.id}/links`, { limit: 25 }, { immediate: !!mod.value.id });

const files = ref<File[]>(asyncFiles.value?.data ?? []);
const links = ref<Link[]>(asyncLinks.value?.data ?? []);

const showEditFile = ref(false);
const showEditLink = ref(false);
const currentFile = ref<File>();
const currentLink = ref<Link>();
const currentLinkIndex = ref<number>();
const changeFile = ref<HTMLInputElement>();
const canModerate = computed(() => hasPermission('manage-mods', mod.value.game));

const allowedStorage = computed(() => mod.value.allowed_storage ? (mod.value.allowed_storage * Math.pow(1024, 2)) : null);
const maxSize = computed(() => allowedStorage.value || settings?.mod_storage_size || 0);

const usedFileSize = computed(() => files.value.reduce((prev, curr) => prev + curr.size, 0) ?? 0);
const usedSizePercent = computed(() => 100 * (usedFileSize.value / maxSize.value));
const usedSizeText = computed(() => {
    const current = friendlySize(usedFileSize.value), total = friendlySize(maxSize.value);
    const percent = usedSizePercent.value.toFixed(1);
    return `${current}/${total} (${percent}%)`;
});
const fileSizeColor =  computed(() => usedSizePercent.value > 80 ? 'danger' : 'primary');

const uploadLink = computed(() => `mods/${mod.value.id}/files`);

// Handle mod submit
watch(() => mod.value.id, async () => {
    for (const link of links.value) {
        if (!link.id) {
            const newLink = await postRequest<Link>(`mods/${mod.value.id}/links`, link);
            Object.assign(link, newLink);
        }
    }
});

function editFile(file: File) {
    showEditFile.value = true;
    currentFile.value = file;
    if (changeFile.value) {
        changeFile.value.value = '';
    }
}

async function saveEditFile(error) {
    try {
        const file = currentFile.value;
        if (file) {
            const formData = new FormData();
            const uploadFile = changeFile.value?.files?.[0];
            if (uploadFile) {
                formData.append('change_file', uploadFile);
            }
            
            for (const [key, value] of Object.entries(file)) {
                formData.append(key, value ? value : '');
            }

            await patchRequest(`files/${file.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            for (const f of files.value) {
                if (f.id === file.id) {
                    Object.assign(f, file);
                }
            }
    
            showEditFile.value = false;
        }
    } catch (e) {
        error(e);
    }
}

function updateHasDownload() {
    mod.value.has_download = (files.value && files.value.length > 0) || (links.value && links.value.length > 0) || false;
    mod.value.files_count = files.value.length ?? 0;
    mod.value.links_count = links.value.length ?? 0;

    if (Math.abs(mod.value.files_count - mod.value.links_count) === 1) {
        mod.value.download = files.value[0] ?? links.value[0];
    }
}

function editLink(link: Link) {
    showEditLink.value = true;
    currentLink.value = clone(link);
    currentLinkIndex.value = links.value.indexOf(link);
}

async function deleteLink(link: Link) {
    if (link.id) {
        await deleteRequest(`links/${link.id}`);
    }
    links.value = links.value.filter(l => l !== link);

    updateHasDownload();
}

function createNewLink() {
    editLink(clone({
        id: -1,
        user_id: user!.id,
        mod_id: mod.value.id,
        name: '',
        desc: '',
        url: '',
        label: '',
        version: ''
    }));
}

async function saveEditLink(error) {
    let link = currentLink.value;

    try {
        if (link) {
            if (link.id == -1) {
                if (mod.value.id) {
                    const newLink = await postRequest<Link>(`mods/${mod.value.id}/links`, link);
                    links.value.push(newLink);
                } else {
                    link.id = 0;
                    links.value.push(clone(link));
                }
            } else if (link.id) {
                link = await patchRequest(`links/${link.id}`, link);
            }

            if (currentLinkIndex.value !== undefined && currentLinkIndex.value !== -1) {
                Object.assign(links.value[currentLinkIndex.value], link);
            }
            
            updateHasDownload();

            currentLinkIndex.value = undefined;
            currentLink.value = undefined;
        }

        showEditLink.value = false;
    } catch (e) {
        error(e);
    }
}

function fileDeleted(file: File) {
    if (mod.value.download_id === file.id) {
        setPrimaryDownload();
    }

    updateHasDownload();
}

function setPrimaryDownload(type?: 'file'|'link', download?: File|Link) {
    mod.value.download_type = type ?? null;
    mod.value.download_id = (download && download.id) ?? null;
    mod.value.download = download;
}
</script>