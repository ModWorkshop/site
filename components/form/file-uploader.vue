<template>
    <flex column gap="1" class="w-full" style="min-height: 150px;" @dragover.prevent="" @drop.prevent="e => upload(e.dataTransfer.files)">
        <label :class="classes" :for="`${name}-file-browser-open`">
            <span class="text-2xl">
                {{$t('file_uploader_drop')}}
                <template v-if="maxFiles">({{files.length}}/{{maxFiles}})</template>
            </span>
        </label>
        <input 
            :id="`${name}-file-browser-open`"
            ref="input" 
            :disabled="reachedMaxFiles"
            type="file"
            hidden
            multiple
            @change="e => upload((e.target as HTMLInputElement).files)"
        >
        <div v-if="list" class="p-3 alt-content-bg round">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>{{$t('name')}}</th>
                        <th>{{$t('file_size')}}</th>
                        <th>{{$t('upload_date')}}</th>
                        <th class="text-center">{{$t('actions')}}</th>
                        <slot name="headers"/>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="file of files" :key="file.created_at">
                        <td>{{file.name}}</td>
                        <td>{{friendlySize(file.size)}}</td>
                        <td v-if="file.progress">{{$t('uploading', [file.progress])}} </td>
                        <td v-else>{{fullDate(file.created_at)}}</td>
                        <td class="text-center p-1">
                            <flex inline>
                                <slot name="buttons" :file="file"/>
                                <a-button icon="mdi:trash" @click.prevent="handleRemove(file)"/>
                            </flex>
                        </td>
                        <slot name="rows" :file="file"/>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="grid file-list p-3 mb-8 alt-content-bg">
            <div v-for="file of files" :key="file.created_at" class="file-item" @click.prevent>
                <a-img class="file-thumbnail" :src="getFileThumb(file)" :url-prefix="urlPrefix"/>
                <flex class="file-options">
                    <div v-if="file.progress" class="file-progress" :style="{width: file.progress + '%'}"/>
                    <flex column class="file-buttons">
                        <span class="self-center">{{friendlySize(file.size)}}</span>
                        <slot name="buttons" :file="file"/>
                        <a-button icon="mdi:trash" @click="handleRemove(file)">{{$t('delete')}}</a-button>
                    </flex>
                </flex>
            </div>
        </div>
    </flex>
</template>

<script setup lang="ts">
import { friendlySize, fullDate } from '~~/utils/helpers';
import { File as MWSFile, SimpleFile } from '~~/types/models';
import { DateTime } from 'luxon';
import axios, { AxiosError, Canceler } from 'axios';
import { useI18n } from 'vue-i18n';

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
    files: UploadFile[],
    extensions?: string[]
    maxFileSize?: number|string,
    maxSize: number|string,
    maxFiles?: number|string
}>();

const { public: config } = useRuntimeConfig();
const { showToast } = useToaster();
const { t } = useI18n();
const showErrorToast = useQuickErrorToast();

function getFileThumb(file) {
    if (file.thumbnail) {
        return file.thumbnail;
    }

    let thumb = props.useFileAsThumb ? file.file : null;
    if (file.has_thumb) {
        thumb = 'thumbnail_' + thumb;
    }

    return thumb;
}

const classes = computed(() => {
    return {
        'alt-content-bg': true,
        round: true,
        'p-6': true,
        'text-center': true,
        'upload-area': true,
        'upload-area-disabled': reachedMaxFiles.value
    };
});

type UploadFile = SimpleFile & {
    name?: string,
    cancel?: Canceler,
    progress?: number,
    thumbnail?: string,
}

const filesArr = toRef(props, 'files');
const input = ref();
const reachedMaxFiles = computed<boolean>(() => props.maxFiles && filesArr.value.length >= props.maxFiles || false);
const usedSize = computed(() => filesArr.value.reduce((prev, curr) => prev + curr.size, 0));

const maxFileSizeBytes = computed(() => parseInt((props.maxFileSize || props.maxSize) as string) * Math.pow(1024, 2));
const maxSizeBytes = computed(() => parseInt(props.maxSize as string) * Math.pow(1024, 2));

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
async function upload(files: FileList|null) {
    if (!files) {
        return;
    }

    for (const file of files) {
        if (file.size > maxFileSizeBytes.value) {
            showToast({ 
                desc: t('file_name_too_large', { name: file.name }),
                color: 'danger'
            });
            continue;
        }

        if (file.size + usedSize.value > maxSizeBytes.value) {
            showToast({ 
                desc: t('file_name_too_large_max_size', { name: file.name }),
                color: 'danger'
            });
            continue;
        }

        if (reachedMaxFiles.value) {
            showToast({ desc: `You cannot upload more files`, color: 'danger' });
        }
        else {
            const insertFile: UploadFile = {
                id: -1,
                created_at: DateTime.now().toISO(),
                name: file.name,
                size: file.size,
                file: '',
                type: ''
            };
    
           //Read the file and get blob src
            let reader = new FileReader();
            reader.onload = () => {
                insertFile.thumbnail = reader.result as string;
                filesArr.value.unshift(insertFile);
                emit('file-begin', insertFile);
            };
            reader.readAsDataURL(file);
    
            const formData = new FormData();
            formData.append('file', file);
            
            try {
    
                const { data } = await axios.post<MWSFile>(props.url, formData, {
                    withCredentials: true,
                    baseURL: config.apiUrl,
                    headers: {'Content-Type': 'multipart/form-data'},
                    onUploadProgress: function(progressEvent) {
                        const reactiveFile = filesArr.value[0];
                        if (progressEvent.progress) {
                            reactiveFile.progress = Math.round(100 * progressEvent.progress);
                        }
                    },
                    cancelToken: new axios.CancelToken(c => insertFile.cancel = c)
                });
    
                const reactiveFile = filesArr.value[0];
                Object.assign(reactiveFile, data);
                reactiveFile.cancel = undefined;
                reactiveFile.progress = undefined;
    
                emit('file-uploaded', reactiveFile);
            } catch (e) {
                if (e instanceof AxiosError) {
                    input.value.value = null;
                    removeFile(insertFile);
                    showErrorToast(e, {}, t('failed_upload'));
                }
            }
        }
    }
    input.value.value = null;
}

/**
 * Handles removing files
 */
async function handleRemove(file: UploadFile) {
    if (file.cancel) {
        file.cancel('cancelled');
    } else {
        await useDelete(`${props.url}/${file.id}`);
    }

    removeFile(file);
    emit('file-deleted', file);
}
</script>

<style>
.upload-area {
    cursor: pointer;
}

.upload-area-disabled {
    cursor: inherit;
    opacity: 0.5;
}

.file-list {
    justify-content: center;
    grid-template-columns: repeat(auto-fill, 200px);
    gap: 0.25rem;
}

.file-thumbnail {
    width: 100%;
    height: 200px;
}

.file-item {
    display: flex;
    position: relative;
}

.file-progress {
    margin-top: auto;
    height: 3px;
    z-index: 10;
    background-color: var(--primary-color);
}

.file-options {
    position: absolute;
    width: 100%;
    height: 100%;
}

.file-buttons {
    position: absolute;
    justify-content: center;
    align-items: stretch;
    gap: 0.25rem;
    width: 100%;
    height: 100%;
    padding: 1.25rem;
    background-color: rgba(0, 0, 0, 0.5);
    transition: opacity 0.25s ease-in-out;
    opacity: flex;
    opacity: 0;
}  

.file-item:hover .file-buttons {
    opacity: 1;
    transition: opacity 0.25s ease-in-out;
}

.file-item img {
    object-fit: cover;
}
</style>