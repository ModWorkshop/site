<template>
    <flex column gap="1"
        style="width: 100%; min-height: 150px;" 
        @dragover.prevent=""
        @drop.prevent="e => upload(e.dataTransfer.files)"
    >
        <label class="alt-bg-color" style="border: dotted #7979797a;" :for="`${name}-file-browser-open`">
            <div class="p-6 cursor-pointer text-center ">
                <h2>Drop files here or click the area to upload files</h2>
            </div>
        </label>
        <input :id="`${name}-file-browser-open`" type="file" hidden multiple @change="e => upload(e.target.files)">
        <div v-if="list" class="p-3 alt-bg-color">
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
                    <tr v-for="file of files" :key="file.trackingId || file.id">
                        <td>{{file.name}}</td>
                        <td>{{friendlySize(file.size)}}</td>
                        <td v-if="file.created_at">{{fullDate(file.created_at)}}</td>
                        <td v-else>Uploading</td>
                        <td class="text-center">
                            <slot name="buttons" :file="file"/>
                            <span class="file-button cursor-pointer" @click.prevent="handleRemove(file)">
                                <font-awesome-icon icon="trash"/>
                            </span>
                        </td>
                        <slot name="rows" :file="file"/>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="grid file-list p-3 mb-8 alt-bg-color">
            <div v-for="file of files" :key="file.trackingId || file.id" class="file-item" @click.prevent>
                <img class="file-thumbnail" :src="file.url" alt="">
                <flex class="file-options">
                    <div v-if="file.progress != -1" class="file-progress" :style="{width: file.progress + '%'}"/>
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

const { init } = useToast();

type UploadFile = File & {
    trackingId?: number,
    signal: AbortSignal,
    progress: number,
    url: string,
    cancel: number
}

const emit = defineEmits([
    'file-begin',
    'file-uploaded',
]);

const props = defineProps<{
    list: boolean,
    url: string,
    name: string,
    files: Array<UploadFile>,
}>();

const filesArr = toRef(props, 'files');

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
            trackingId: Date.now(),
            name: file.name,
            url: '',
            size: file.size,
            progress: -1,
            signal: null
        };

       //Read the file and get blob src
        let reader = new FileReader();
        reader.onload = () => {
            insertFile.url = reader.result as string;
            filesArr.value.push(insertFile);
            emit('file-begin', insertFile);
            fileIndex = filesArr.value.length - 1;
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('file', file);
        const controller = new AbortController();
        const data = await usePost<File>(props.url, formData, {
            // onUploadProgress: function(progressEvent) {
            //     insertFile.progress = Math.round(100 * (progressEvent.loaded / progressEvent.total));
            // },
            signal: controller.signal
        }).catch(err => {
            removeFile(insertFile);
            console.log(err.data.message);
            
            init('File failed to upload: ' + err.data.message);
        });

        if (data) {
            const reactiveFile = filesArr.value[fileIndex];
            reactiveFile.id = data.id;
            reactiveFile.name = data.name || data.file;
            reactiveFile.size = data.size;
            reactiveFile.created_at = data.created_at;
            reactiveFile.url = `http://localhost:8000/storage/images/${data.file}`;
            reactiveFile.signal = null;

            emit('file-uploaded', reactiveFile);
        }
    }
}

/**
 * Handles removing files
 */
async function handleRemove(file) {
    if (!file.signal) {
        await useDelete(`${props.url}/${file.id}`);
    }
    removeFile(file);
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