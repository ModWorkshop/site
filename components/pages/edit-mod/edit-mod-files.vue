<template>
    <flex column gap="4">
        <a-input v-model="mod.version" label="Version"/>
        <md-editor v-model="mod.changelog" label="Changelog" rows="12"/>
        <a-select v-model="mod.download_id" label="Primary Download" desc="If your mod is primarily a single download, you may choose the primary file or link the mod uses" placeholder="Select file or link" clearable :options="downloads"/>
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
                                    <a-button icon="trash" @click.prevent="editLink(link)"/>
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
        <uploader list name="files" :url="uploadLink" :files="files" @file-uploaded="fileUploaded" @file-deleted="fileDeleted">
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
        </uploader>
        <client-only>
            <va-modal v-model="showEditLink" size="large" background-color="#2b3036" no-outside-dismiss>
                <template #content="{ ok }">
                    <flex v-if="currentLink" column gap="2">
                        <h2>Edit Link</h2>
                        <a-input v-model="currentLink.name" label="name"/>
                        <a-input v-model="currentLink.label" label="label"/>
                        <a-input v-model="currentLink.url" label="url"/>
                        <a-input v-model="currentLink.version" label="version"/>
                        <md-editor v-model="currentLink.desc" rows="8" label="desc"/>
                        <a-error-alert :error="linksError"/>
                        <flex>
                            <a-button @click="saveEditLink(currentLink, ok)">Save</a-button>
                            <a-button color="danger" @click="closeModals(ok)">Cancel</a-button>
                        </flex>
                    </flex>
                </template>
            </va-modal>
            <va-modal v-model="showEditFile" size="large" background-color="#2b3036" no-outside-dismiss>
                <template #content="{ ok }">
                    <flex v-if="currentFile" column gap="2">
                        <h2>Edit File</h2>
                        <a-input v-model="currentFile.name" label="name"/>
                        <a-input v-model="currentFile.label" label="label"/>
                        <a-input v-model="currentFile.version" label="version"/>
                        <md-editor v-model="currentFile.desc" rows="8" label="desc"/>
                        <a-error-alert :error="filesError"/>
                        <flex>
                            <a-button @click="saveEditFile(currentFile, ok)">Save</a-button>
                            <a-button color="danger" @click="closeModals(ok)">Cancel</a-button>
                        </flex>
                    </flex>
                </template>
            </va-modal>
        </client-only>
    </flex>
</template>

<script setup lang="ts">
import { File, Link, Mod } from '~~/types/models';
import clone from 'rfdc/default';

const props = defineProps<{
    mod: Mod,
    canSave: boolean
}>();

const files = ref(clone(props.mod.files));
const showEditFile = ref(false);
const showEditLink = ref(false);
const currentFile = ref<File>();
const currentLink = ref<Link>();
const linksError = ref(null);
const filesError = ref(null);

const downloads = computed(() => {
    return [...files.value, ...props.mod.links];
});

const uploadLink = computed(() => props.mod !== null ? `mods/${props.mod.id}/files`: '');
const ignoreChanges: () => void = inject('ignoreChanges');

function editFile(file: File) {
    showEditFile.value = true;
    currentFile.value = file;
}

async function saveEditFile(file: File, ok: () => void) {
    filesError.value = null;

    await usePatch(`mods/${props.mod.id}/files/${file.id}`, file).catch(err => filesError.value = err);
    
    if(filesError.value) {
        return;
    }

    for (const f of props.mod.files) {
        if (f.id === file.id) {
            Object.assign(f, file);
        }
    }

    if (!props.canSave) {
        ignoreChanges();
    }

    ok();
}

function editLink(link: Link) {
    showEditLink.value = true;
    currentLink.value = link;
}

function createNewLink() {
    editLink(clone({
        id: -1,
        url: '',
        desc: '',
        label: '',
    }));
}

function closeModals(ok: () => void) {
    linksError.value = null;
    filesError.value = null;
    ok();
}
async function saveEditLink(link: Link, ok: () => void) {
    linksError.value = null;

    if (link.id == -1) {
        const newLink = await usePost<Link>(`mods/${props.mod.id}/links`, link).catch(err => linksError.value = err);
        if (newLink) {
            props.mod.links.push(newLink);
        }
    } else {
        await usePatch(`mods/${props.mod.id}/links/${link.id}`, link).catch(err => linksError.value = err);
    }
    
    if(linksError.value) {
        return;
    }

    for (const f of props.mod.links) {
        if (f.id === link.id) {
            Object.assign(f, link);
        }
    }

    if (!props.canSave) {
        ignoreChanges();
    }

    ok();
}

function fileUploaded(file: File) {
    props.mod.files.push(file);
    //If we have changes already we don't want to ignore the changes
    //We ignore them since the changes are already "applied" due to files being instantly uploaded.
    if (!props.canSave) {
        ignoreChanges();
    }
}

function fileDeleted(file: File) {
    for (const [i, f] of Object.entries(props.mod.files)) {
        if (toRaw(f) === toRaw(file)) {
            props.mod.files.splice(parseInt(i), 1);
        }
    }

    if (props.mod.download_id === file.id) {
        setPrimaryDownload(null, null);
    }

    if (!props.canSave) {
        ignoreChanges();
    }
}

function setPrimaryDownload(type: 'file'|'link', download: File|Link) {
    props.mod.download_type = type;
    props.mod.download_id = download && download.id;
    props.mod.download = download;
}
</script>
<style scoped>

</style>