<template>
    <a-input v-model="mod.version" :label="$t('version')"/>
    <md-editor v-model="mod.changelog" :label="$t('changelog')" rows="12"/>
    <a-select 
        v-model="primaryDownload"
        :label="$t('primary_download')"
        :desc="$t('primary_download_desc')"
        clearable :options="downloads"
        value-by=""
        @update:model-value="(item: Download) => setPrimaryDownload(item.download_type, item)"
    />
    <div>
        <flex class="items-center">
            <label>{{$t('links')}}</label>
            <a-button class="ml-auto mb-1" icon="mdi:plus-thick" @click="createNewLink"/>
        </flex>
        <flex column class="alt-content-bg p-3">
            <table>
                <thead>
                    <tr>
                        <th>{{$t('name')}}</th>
                        <th>{{$t('url')}}</th>
                        <th>{{$t('upload_date')}}</th>
                        <th class="text-center">{{$t('actions')}}</th>
                        <th class="text-center">{{$t('primary')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="link of mod.links" :key="link.id">
                        <td>{{link.name}}</td>
                        <td>{{link.url}}</td>
                        <td>{{fullDate(link.updated_at)}}</td>
                        <td class="text-center p-1">
                            <flex inline>
                                <a-button icon="mdi:cog" @click.prevent="editLink(link)"/>
                                <a-button icon="mdi:trash" @click.prevent="deleteLink(link)"/>
                            </flex>
                        </td>
                        <td class="text-center">
                            <input :checked="(link.id === mod.download_id && mod.download_type == 'link') ? true : undefined" type="radio" @change="setPrimaryDownload('link', link)">
                        </td>
                    </tr>
                </tbody>
            </table>
        </flex>
    </div>
    <label>{{$t('files')}}</label>
    <small>{{$t('allowed_size_per_mod', [friendlySize(maxSize)])}}</small>
    <a-progress :percent="usedSizePercent" :text="usedSizeText" :color="fileSizeColor"/>
    <file-uploader list name="files" :url="uploadLink" :files="filesCopy" :max-size="settings.max_file_size / Math.pow(1024, 2)" @file-uploaded="fileUploaded" @file-deleted="fileDeleted">
        <template #headers>
            <td class="text-center">{{$t('primary')}}</td>
        </template>
        <template #rows="{file}">
            <td class="text-center">
                <input :checked="(file.id === mod.download_id && mod.download_type == 'file') ? true : undefined" type="radio" @change="setPrimaryDownload('file', file as File)">
            </td>
        </template>
        <template #buttons="{file}">
            <a-button class="file-button" icon="mdi:cog" @click.prevent="editFile(file as File)"/>
        </template>
    </file-uploader>
    <a-input v-if="canModerate" v-model="mod.allowed_storage" type="number" max="1000" :label="$t('allowed_storage')" :desc="$t('allowed_storage_help')"/>
    <a-modal-form v-if="currentLink" v-model="showEditLink" :title="$t('edit_link')" @submit="saveEditLink">
        <a-input v-model="currentLink.name" label="name"/>
        <a-input v-model="currentLink.label" label="label"/>
        <a-input v-model="currentLink.url" type="url" label="url"/>
        <a-input v-model="currentLink.version" label="version"/>
        <md-editor v-model="currentLink.desc" rows="8" label="desc"/>
    </a-modal-form>
    <a-modal-form v-if="currentFile" v-model="showEditFile" :title="$t('edit_file')" @submit="saveEditFile">
        <a-input v-model="currentFile.name" label="name"/>
        <a-input v-model="currentFile.label" label="label"/>
        <a-input v-model="currentFile.version" label="version"/>
        <md-editor v-model="currentFile.desc" rows="8" label="desc"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { Download, File, Link, Mod } from '~~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~~/store';
import { friendlySize, fullDate } from '~~/utils/helpers';

const { settings, hasPermission } = useStore();

const props = defineProps<{
    mod: Mod,
    canSave: boolean
}>();

const files = computed<File[]>(() => props.mod.files || []);
const links = computed<Link[]>(() => props.mod.links || []);
const filesCopy = ref<File[]>(clone(files.value));

const showEditFile = ref(false);
const showEditLink = ref(false);
const currentFile = ref<File>();
const currentLink = ref<Link>();
const canModerate = computed(() => hasPermission('manage-mod', props.mod.game));

const allowedStorage = computed(() => props.mod.allowed_storage ? (props.mod.allowed_storage * Math.pow(1024, 2)) : null);
const maxSize = computed(() => allowedStorage.value || settings.mod_storage_size);

const usedFileSize = computed(() => files.value.reduce((prev, curr) => prev + curr.size, 0));
const usedSizePercent = computed(() => 100 * (usedFileSize.value / maxSize.value));
const usedSizeText = computed(() => {
    const current = friendlySize(usedFileSize.value), total = friendlySize(maxSize.value);
    const percent = usedSizePercent.value.toFixed(1);
    return `${current}/${total} (${percent}%)`;
});
const fileSizeColor =  computed(() => usedSizePercent.value > 80 ? 'danger' : 'primary');

const downloads = computed(() => {
    const downloads: Download[] = [];
    filesCopy.value.forEach(file => downloads.push({ download_type: 'file', ...file }));
    links.value.forEach(link => downloads.push({ download_type: 'link', ...link }));
    return downloads;
});

const primaryDownload = computed(() => downloads.value.find(file => file.download_type == props.mod.download_type && file.id === props.mod.download_id));

const uploadLink = computed(() => props.mod !== null ? `mods/${props.mod.id}/files`: '');
const ignoreChanges = inject<() => void>('ignoreChanges');

function editFile(file: File) {
    showEditFile.value = true;
    currentFile.value = file;
}

async function saveEditFile(error) {
    try {
        const file = currentFile.value;
        if (file) {
            await usePatch(`mods/${props.mod.id}/files/${file.id}`, file);
    
            for (const f of files.value) {
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

function editLink(link: Link) {
    showEditLink.value = true;
    currentLink.value = link;
}

async function deleteLink(link: Link) {
    await useDelete(`mods/${props.mod.id}/links/${link.id}`);
    props.mod.links = links.value.filter(l => l.id !== link.id);

    ignoreChanges?.();
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
                const newLink = await usePost<Link>(`mods/${props.mod.id}/links`, link);
                links.value.push(newLink);
            } else {
                await usePatch(`mods/${props.mod.id}/links/${link.id}`, link);
            }
    
            for (const f of links.value) {
                if (f.id === link.id) {
                    Object.assign(f, link);
                }
            }
            
            ignoreChanges?.();
            showEditLink.value = false;
        }
    } catch (e) {
        error(e);
    }
}

function fileUploaded(file: File) {
    files.value.push(file);
    //If we have changes already we don't want to ignore the changes
    //We ignore them since the changes are already "applied" due to files being instantly uploaded.
    ignoreChanges?.();
}

function fileDeleted(file: File) {
    for (const [i, f] of Object.entries(files.value)) {
        if (f.id === file.id) {
            files.value.splice(parseInt(i), 1);
        }
    }

    if (props.mod.download_id === file.id) {
        setPrimaryDownload();
    }

    ignoreChanges?.();
}

function setPrimaryDownload(type?: 'file'|'link', download?: File|Link) {
    props.mod.download_type = type;
    props.mod.download_id = download && download.id;
    props.mod.download = download;
}
</script>
<style scoped>

</style>