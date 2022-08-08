<template>
    <flex column gap="1" class="w-full" style="min-height: 150px;" @dragover.prevent="" @drop.prevent="e => upload(e.dataTransfer.files)">
        <label class="alt-bg-color round" style="border: dotted #7979797a;" :for="`${name}-file-browser-open`">
            <div class="p-6 cursor-pointer text-center ">
                <h2>Drop files here or click the area to upload files</h2>
            </div>
        </label>
        <input :id="`${name}-file-browser-open`" ref="input" type="file" hidden multiple @change="e => upload((e.target as HTMLInputElement).files)">
        <div v-if="list" class="p-3 alt-bg-color round">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Upload Date</th>
                        <th class="text-center">Actions</th>
                        <slot name="headers"/>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="file of files" :key="file.created_at">
                        <td>{{file.name}}</td>
                        <td>{{friendlySize(file.size)}}</td>
                        <td v-if="file.progress">Uploading: {{file.progress}}% </td>
                        <td v-else>{{fullDate(file.created_at)}}</td>
                        <td class="text-center p-1">
                            <flex inline>
                                <slot name="buttons" :file="file"/>
                                <a-button icon="trash" @click.prevent="handleRemove(file)"/>
                            </flex>
                        </td>
                        <slot name="rows" :file="file"/>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="grid file-list p-3 mb-8 alt-bg-color">
            <div v-for="file of files" :key="file.created_at" class="file-item" @click.prevent>
                <a-img class="file-thumbnail" :src="file.thumbnail || (useFileAsThumb && file.file)" :url-prefix="urlPrefix"/>
                <flex class="file-options">
                    <div v-if="file.progress" class="file-progress" :style="{width: file.progress + '%'}"/>
                    <flex column class="file-buttons">
                        <a-button class="file-button cursor-pointer" icon="trash" @click.prevent="handleRemove(file)">
                            Delete
                        </a-button>
                        <slot name="buttons" :file="file"/>
                    </flex>
                </flex>
            </div>
        </div>
    </flex>
</template>

<script setup lang="ts">
import { friendlySize, fullDate } from '~~/utils/helpers';
import { File } from '~~/types/models';
import { DateTime } from 'luxon';
import axios, { Canceler } from 'axios';
const { public: config } = useRuntimeConfig();

const { init } = useToast();

type UploadFile = {
    id: number,
    name: string,
    file?: string,
    size: number,
    cancel?: Canceler,
    progress?: number,
    thumbnail?: string,
    created_at?: string
}

const emit = defineEmits([
    'file-begin',
    'file-uploaded',
    'file-deleted',
]);

const props = defineProps<{
    list?: boolean,
    url: string,
    urlPrefix?: string,
    useFileAsThumb?: boolean,
    name: string,
    files: Array<UploadFile>,
}>();

const filesArr = toRef(props, 'files');
const input = ref();

function removeFile(file: UploadFile) {
    for (const [k, f] of Object.entries(props.files)) {
        if (toRaw(f) == toRaw(file)) {
            filesArr.value.splice(parseInt(k), 1);
        }
    }
}

/**
 * Handles the actual upload of the file(s)
 */
async function upload(files) {
    for (const file of files) {
        let fileIndex = -1;
        let insertFile: UploadFile = {
            id: -1,
            created_at: DateTime.now().toISO(),
            name: file.name,
            size: file.size,
        };

       //Read the file and get blob src
        let reader = new FileReader();
        reader.onload = () => {
            insertFile.thumbnail = reader.result as string;
            filesArr.value.push(insertFile);
            emit('file-begin', insertFile);
            fileIndex = filesArr.value.length - 1;
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('file', file);
        
        try {

            const { data } = await axios.post<File>(props.url, formData, {
                withCredentials: true,
                baseURL: config.apiUrl,
                headers: {'Content-Type': 'multipart/form-data'},
                onUploadProgress: function(progressEvent) {
                    const reactiveFile = filesArr.value[fileIndex];
                    reactiveFile.progress = Math.round(100 * (progressEvent.loaded / progressEvent.total));
                },
                cancelToken: new axios.CancelToken(c => insertFile.cancel = c)
            });

            const reactiveFile = filesArr.value[fileIndex];
            Object.assign(reactiveFile, data);
            reactiveFile.cancel = null;
            reactiveFile.progress = null;

            emit('file-uploaded', reactiveFile);
        } catch (err) {
            removeFile(insertFile);
            console.log(err.data.message);
            
            init('File failed to upload: ' + err.data.message);
        }
    }
    input.value.value = null;
}

/**
 * Handles removing files
 */
async function handleRemove(file: UploadFile) {
    if (file.cancel) {
        file.cancel('Cancelled by user');
    } else {
        await useDelete(`${props.url}/${file.id}`);
    }

    removeFile(file);
    emit('file-deleted', file);
}
</script>

<style scoped>
    .file-list {
        grid-template-columns: repeat(auto-fill, 150px);
        justify-content: center;
    }

    .file-thumbnail {
        width: 100%;
        height: 150px;
    }

    .file-item {
        display: flex;
    }

    .file-progress {
        margin-top: auto;
        height: 3px;
        z-index: 10;
        background-color: var(--primary-color);
    }

    .file-options {
        position: absolute;
        width: 150px;
        height: 150px;
    }

    .file-buttons {
        position: absolute;
        justify-content: center;
        align-items: center;
        gap: 0.25rem;
        width: 100%;
        height: 100%;
        background-color: rgba(36, 36, 36, 0.5);
        display: none;
    }

    .file-item:hover .file-buttons {
        display: flex;
    }

    .file-item img {
        object-fit: cover;
    }
</style>