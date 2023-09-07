<template>
    <a-input v-if="!light" v-model="mod.version" :label="$t('version')"/>
    <md-editor v-if="!light" v-model="mod.changelog" :label="$t('changelog')" rows="12"/>
    <a-button class="mr-auto" @click="setPrimaryDownload()"><i-mdi-close/> {{ $t('clear_primary_download') }}</a-button>

    <label>{{$t('files')}}</label>
    <flex column>
        <small>{{$t('allowed_size_per_mod', [friendlySize(maxSize)])}}</small>
        <a-progress :percent="usedSizePercent" :text="usedSizeText" :color="fileSizeColor"/>
        <file-uploader 
            list
            name="files"
            :upload-url="uploadLink"
            max-files="25" 
            :files="files?.data ?? []" 
            :max-size="(settings?.max_file_size || 0) / Math.pow(1024, 2)" 
            url="files"
            @file-uploaded="updateHasDownload"
            @file-deleted="fileDeleted"
        >
            <template #headers>
                <td class="text-center">{{$t('primary')}}</td>
            </template>
            <template #rows="{file}">
                <td class="text-center">
                    <input :checked="(file.id === mod.download_id && mod.download_type == 'file') ? true : undefined" type="radio" @change="setPrimaryDownload('file', file as File)">
                </td>
            </template>
            <template #buttons="{file}">
                <a-button class="file-button" @click.prevent="editFile(file as File)">
                    <i-mdi-cog/>
                </a-button>
            </template>
        </file-uploader>
    </flex>

    <flex column>
        <flex class="items-center">
            <label>{{$t('links')}}</label>
            <a-button v-if="links && links.data.length < 25" class="ml-auto mb-2" @click="createNewLink">
                <i-mdi-plus-thick/>
            </a-button>
        </flex>
        <flex column class="alt-content-bg p-3">
            <table v-if="links?.data.length">
                <thead>
                    <tr>
                        <th>{{$t('name')}}</th>
                        <th>{{$t('url')}}</th>
                        <th>{{$t('date')}}</th>
                        <th class="text-center">{{$t('actions')}}</th>
                        <th class="text-center">{{$t('primary')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="link of links?.data" :key="link.id">
                        <td>{{link.name}}</td>
                        <td>{{link.url}}</td>
                        <td>{{fullDate(link.updated_at)}}</td>
                        <td class="text-center p-1">
                            <flex inline>
                                <a-button @click.prevent="editLink(link)"><i-mdi-cog/></a-button>
                                <a-button @click.prevent="deleteLink(link)"><i-mdi-delete/></a-button>
                            </flex>
                        </td>
                        <td class="text-center">
                            <input :checked="(link.id === mod.download_id && mod.download_type == 'link') ? true : undefined" type="radio" @change="setPrimaryDownload('link', link)">
                        </td>
                    </tr>
                </tbody>
            </table>
            <span v-else class="text-3xl mx-auto">{{$t('links_help')}}</span>
        </flex>
    </flex>

    <a-input v-if="canModerate && !light" v-model="mod.allowed_storage" type="number" max="1000" :label="$t('allowed_storage')" :desc="$t('allowed_storage_help')"/>
    <a-modal-form v-if="currentLink" v-model="showEditLink" :title="$t('edit_link')" @submit="saveEditLink">
        <a-input v-model="currentLink.name" :label="$t('name')"/>
        <a-input v-model="currentLink.label" :label="$t('label')"/>
        <a-input v-model="currentLink.url" type="url" :label="$t('url')"/>
        <a-input v-model="currentLink.version" :label="$t('version')"/>
        <a-select v-model="currentLink.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable>
            <template #any-option="{ option }">
                <a-img style="width: 150px; height: 150px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
            </template>
        </a-select>
        <md-editor v-model="currentLink.desc" rows="8" :label="$t('description')"/>
    </a-modal-form>
    <a-modal-form v-if="currentFile" v-model="showEditFile" :title="$t('edit_file')" @submit="saveEditFile">
        <a-input v-model="currentFile.name" :label="$t('name')"/>
        <a-input v-model="currentFile.label" :label="$t('label')"/>
        <a-input v-model="currentFile.version" :label="$t('version')"/>
        <a-input v-model:elementRef="changeFile" type="file" :label="$t('upload_file')"/>
        <a-select v-model="currentFile.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable>
            <template #any-option="{ option }">
                <a-img style="width: 150px; height: 150px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
            </template>
        </a-select>
        <md-editor v-model="currentFile.desc" rows="8" :label="$t('description')"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { File, Link, Mod } from '~~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~~/store';
import { friendlySize, fullDate } from '~~/utils/helpers';

const { settings, hasPermission } = useStore();

defineProps<{
    light?: boolean
}>();

const mod = defineModel<Mod>({ required: true });

const { data: files } = await useWatchedFetchMany(`mods/${mod.value.id}/files`, { limit: 25 });
const { data: links } = await useWatchedFetchMany(`mods/${mod.value.id}/links`, { limit: 25 });

const showEditFile = ref(false);
const showEditLink = ref(false);
const currentFile = ref<File>();
const currentLink = ref<Link>();
const changeFile = ref<HTMLInputElement>();
const canModerate = computed(() => hasPermission('manage-mods', mod.value.game));

const allowedStorage = computed(() => mod.value.allowed_storage ? (mod.value.allowed_storage * Math.pow(1024, 2)) : null);
const maxSize = computed(() => allowedStorage.value || settings?.mod_storage_size || 0);

const usedFileSize = computed(() => files.value?.data.reduce((prev, curr) => prev + curr.size, 0));
const usedSizePercent = computed(() => 100 * (usedFileSize.value / maxSize.value));
const usedSizeText = computed(() => {
    const current = friendlySize(usedFileSize.value), total = friendlySize(maxSize.value);
    const percent = usedSizePercent.value.toFixed(1);
    return `${current}/${total} (${percent}%)`;
});
const fileSizeColor =  computed(() => usedSizePercent.value > 80 ? 'danger' : 'primary');

const uploadLink = computed(() => `mods/${mod.value.id}/files`);

const ignoreChanges = inject<() => void>('ignoreChanges');

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

            for (const f of files.value!.data) {
                if (f.id === file.id) {
                    Object.assign(f, file);
                }
            }
    
            ignoreChanges?.();
            showEditFile.value = false;
        }
    } catch (e) {
        error(e);
    }
}

function updateHasDownload() {
    mod.value.has_download = (files.value && files.value.data.length > 0) || (links.value && links.value.data.length > 0) || false;
    ignoreChanges?.();
}

function editLink(link: Link) {
    showEditLink.value = true;
    currentLink.value = link;
}

async function deleteLink(link: Link) {
    await deleteRequest(`links/${link.id}`);
    links.value!.data = links.value!.data.filter(l => l.id !== link.id);

    updateHasDownload();
}

function createNewLink() {
    editLink(clone({
        id: -1,
        user_id: -1,
        mod_id: -1,
        name: '',
        desc: '',
        url: '',
        label: '',
        version: ''
    }));
}

async function saveEditLink(error) {
    const link = currentLink.value;

    try {
        if (link) {
            if (link.id == -1) {
                const newLink = await postRequest<Link>(`mods/${mod.value.id}/links`, link);
                if (links.value) {
                    links.value.data.push(newLink);
                }
            } else {
                await patchRequest(`links/${link.id}`, link);
            }
    
            if (links.value) {
                for (const f of links.value.data) {
                    if (f.id === link.id) {
                        Object.assign(f, link);
                    }
                }
            }
            
            
            updateHasDownload();
            showEditLink.value = false;
        }

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