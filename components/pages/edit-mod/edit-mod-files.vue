<template>
    <flex column gap="4">
        <a-input v-model="mod.version" label="Version"/>
        <md-editor v-model="mod.changelog" label="Changelog" rows="12"/>
        <a-select v-model="mod.download_id" label="Primary Download" desc="If your mod is primarily a single download, you may choose the primary file or link the mod uses" placeholder="Select file or link" clearable :options="files"/>
        <uploader list name="files" :url="uploadLink" :files="files" @file-uploaded="fileUploaded" @file-deleted="fileDeleted">
            <template #headers>
                <td class="text-center">Primary</td>
            </template>
            <template #rows="{file}">
                <td class="text-center">
                    <input :checked="file.id === mod.download_id ? true : null" type="radio" @change="setPrimaryDownload('file', file.id)">
                </td>
            </template>
            <template #buttons="{file}">
                <span class="file-button cursor-pointer" @click.prevent="editFile(file)">
                    <font-awesome-icon icon="cog"/>
                </span>
            </template>
        </uploader>
        <client-only>
            <va-modal v-model="showEditFile" size="large" background-color="#2b3036" no-outside-dismiss>
                <template #content="{ ok }">
                    <flex v-if="currentFile" column gap="2">
                        <h2>Edit File</h2>
                        <a-input v-model="currentFile.name" label="name"/>
                        <a-input v-model="currentFile.label" label="label"/>
                        <md-editor v-model="currentFile.desc" rows="8" label="desc"/>
                        <flex>
                            <a-button @click="saveEditFile(currentFile, ok)">Save</a-button>
                            <a-button color="danger" @click="ok">Cancel</a-button>
                        </flex>
                    </flex>
                </template>
            </va-modal>
        </client-only>
    </flex>
</template>

<script setup lang="ts">
import { File, Mod } from '~~/types/models';
import clone from 'rfdc/default';

const props = defineProps<{
    mod: Mod,
    canSave: boolean
}>();

const files = ref(clone(props.mod.files));
const showEditFile = ref(false);
const currentFile = ref<File>();

const uploadLink = computed(() => props.mod !== null ? `mods/${props.mod.id}/files`: '');
const ignoreChanges: () => void = inject('ignoreChanges');

function editFile(file: File) {
    showEditFile.value = true;
    currentFile.value = file;
}

async function saveEditFile(file: File, ok: () => void) {
    await usePatch(`files/${file.id}`, file);

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

    if (!props.canSave) {
        ignoreChanges();
    }
}
function setPrimaryDownload(type: string, id: number) {
    props.mod.download_type = type;
    props.mod.download_id = id;
}
</script>
<style scoped>

</style>