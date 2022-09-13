<template>
    <a-input v-model="mod.version" label="Version"/>
    <md-editor v-model="mod.changelog" label="Changelog" rows="12"/>
    <a-select 
        v-model="primaryDownload"
        label="Primary Download"
        :desc="$t('primary_download_desc')"
        placeholder="Select file or link"
        clearable :options="downloads"
        value-by=""
        @update:model-value="item => setPrimaryDownload(item.download_type, item)"
    />
    <div>
        <flex class="items-center">
            <label>Links</label>
            <a-button class="ml-auto" @click="createNewLink">New</a-button>
        </flex>
        <flex column class="alt-bg-color p-3">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Update Date</th>
                        <th class="text-center">Actions</th>
                        <th class="text-center">Primary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="link of mod.links" :key="link.id">
                        <td>{{link.name}}</td>
                        <td>{{link.url}}</td>
                        <td>{{fullDate(link.updated_at)}}</td>
                        <td class="text-center p-1">
                            <flex inline>
                                <a-button icon="cog" @click.prevent="editLink(link)"/>
                                <a-button icon="trash" @click.prevent="deleteLink(link)"/>
                            </flex>
                        </td>
                        <td class="text-center">
                            <input :checked="(link.id === mod.download_id && mod.download_type == 'link') ? true : null" type="radio" @change="setPrimaryDownload('link', link)">
                        </td>
                    </tr>
                </tbody>
            </table>
        </flex>
    </div>
    <label>Files</label>
    <small>{{$t('allowed_size_per_mod', [friendlySize(maxSize)])}}</small>
    <a-progress :percent="usedSizePercent" :text="usedSizeText" :color="fileSizeColor"/>
    <file-uploader list name="files" :url="uploadLink" :files="files" :max-size="settings.max_file_size / Math.pow(1024, 2)" @file-uploaded="fileUploaded" @file-deleted="fileDeleted">
        <template #headers>
            <td class="text-center">Primary</td>
        </template>
        <template #rows="{file}">
            <td class="text-center">
                <input :checked="(file.id === mod.download_id && mod.download_type == 'file') ? true : null" type="radio" @change="setPrimaryDownload('file', file)">
            </td>
        </template>
        <template #buttons="{file}">
            <a-button class="file-button" icon="cog" @click.prevent="editFile(file)"/>
        </template>
    </file-uploader>
    <a-modal-form v-model="showEditLink" title="Edit Link" @save="saveEditLink">
        <a-input v-model="currentLink.name" label="name"/>
        <a-input v-model="currentLink.label" label="label"/>
        <a-input v-model="currentLink.url" type="url" label="url"/>
        <a-input v-model="currentLink.version" label="version"/>
        <md-editor v-model="currentLink.desc" rows="8" label="desc"/>
    </a-modal-form>
    <a-modal-form v-model="showEditFile" title="Edit File" @save="saveEditFile">
        <a-input v-model="currentFile.name" label="name"/>
        <a-input v-model="currentFile.label" label="label"/>
        <a-input v-model="currentFile.version" label="version"/>
        <md-editor v-model="currentFile.desc" rows="8" label="desc"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { File, Link, Mod } from '~~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~~/store';

const { settings } = useStore();

const props = defineProps<{
    mod: Mod,
    canSave: boolean
}>();

const files = ref<File[]>(clone(props.mod.files));
const showEditFile = ref(false);
const showEditLink = ref(false);
const currentFile = ref<File>();
const currentLink = ref<Link>();

const maxSize = settings.mod_storage_size;

const usedFileSize = computed(() => props.mod.files.reduce((prev, curr) => prev + curr.size, 0));
const usedSizePercent = computed(() => 100 * (usedFileSize.value / maxSize));
const usedSizeText = computed(() => {
    const current = friendlySize(usedFileSize.value), total = friendlySize(maxSize);
    const percent = usedSizePercent.value.toPrecision(4);
    return `${current}/${total} (${percent}%)`;
});
const fileSizeColor =  computed(() => usedSizePercent.value > 80 ? 'danger' : 'primary');

const downloads = computed(() => {
    const downloads = [];
    files.value.forEach(file => downloads.push({ download_type: 'file', ...file }));
    props.mod.links.forEach(link => downloads.push({ download_type: 'link', ...link }));
    return downloads;
});

const primaryDownload = computed(() => downloads.value.find(file => file.download_type == props.mod.download_type && file.id === props.mod.download_id));

const uploadLink = computed(() => props.mod !== null ? `mods/${props.mod.id}/files`: '');
const ignoreChanges: () => void = inject('ignoreChanges');

function editFile(file: File) {
    showEditFile.value = true;
    currentFile.value = file;
}

async function saveEditFile(ok, error) {
    try {
        const file = currentFile.value;
        await usePatch(`mods/${props.mod.id}/files/${file.id}`, file);

        for (const f of props.mod.files) {
            if (f.id === file.id) {
                Object.assign(f, file);
            }
        }

        ignoreChanges();
        ok();
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
    props.mod.links = props.mod.links.filter(l => l.id !== link.id);

    ignoreChanges();
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

async function saveEditLink(ok, error) {
    const link = currentLink.value;

    try {
        if (link.id == -1) {
            const newLink = await usePost<Link>(`mods/${props.mod.id}/links`, link);
            props.mod.links.push(newLink);
        } else {
            await usePatch(`mods/${props.mod.id}/links/${link.id}`, link);
        }

        for (const f of props.mod.links) {
            if (f.id === link.id) {
                Object.assign(f, link);
            }
        }
        
        ignoreChanges();
        ok();
    } catch (e) {
        error(e);
    }
}

function fileUploaded(file: File) {
    props.mod.files.push(file);
    //If we have changes already we don't want to ignore the changes
    //We ignore them since the changes are already "applied" due to files being instantly uploaded.
    ignoreChanges();
}

function fileDeleted(file: File) {
    for (const [i, f] of Object.entries(props.mod.files)) {
        if (f.id === file.id) {
            props.mod.files.splice(parseInt(i), 1);
        }
    }

    if (props.mod.download_id === file.id) {
        setPrimaryDownload(null, null);
    }

    ignoreChanges();
}

function setPrimaryDownload(type: 'file'|'link', download: File|Link) {
    props.mod.download_type = type;
    props.mod.download_id = download && download.id;
    props.mod.download = download;
}
</script>
<style scoped>

</style>