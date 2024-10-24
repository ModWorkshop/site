<template>
    <m-flex column gap="1" class="w-full" @dragover.prevent="" @drop.prevent="e => register(e.dataTransfer.files)">
        <label :class="classes" :for="`${name}-file-browser-open`">
            <span class="text-3xl">
                {{$t('file_uploader_drop')}}
                <template v-if="maxFiles">({{vm.length}}/{{maxFiles}})</template>
            </span>
        </label>
        <input 
            :id="`${name}-file-browser-open`"
            ref="input" 
            :disabled="disabled || reachedMaxFiles"
            type="file"
            hidden
            multiple
            @change="e => register((e.target as HTMLInputElement).files)"
        >
        <m-flex v-if="list" column>
            <m-uploader-list-file
                v-for="file of uploadingFiles"
                :key="file.created_at"
                :file="file"
                :paused="paused"
                :paused-reason="pausedReason"
                @remove="removeFileDialog"
            >
                <template #before-info>
                    <slot name="before-info" :file="file"/>
                </template>
                <template #after-info>
                    <slot name="after-info" :file="file"/>
                </template>
                <template #before-buttons>
                    <slot name="before-buttons" :file="file"/>
                </template>
                <template #after-buttons>
                    <slot name="after-buttons" :file="file"/>
                </template>
            </m-uploader-list-file>

            <template v-if="vm.length">
                <m-uploader-list-file 
                    v-for="file of vm"
                    :key="file.created_at"
                    :file="file"
                    :paused="paused"
                    :paused-reason="pausedReason"
                    @remove="removeFileDialog"
                >
                    <template #before-info>
                        <slot name="before-info" :file="file"/>
                    </template>
                    <template #after-info>
                        <slot name="after-info" :file="file"/>
                    </template>
                    <template #before-buttons>
                        <slot name="before-buttons" :file="file"/>
                    </template>
                    <template #after-buttons>
                        <slot name="after-buttons" :file="file"/>
                    </template>
                </m-uploader-list-file>
            </template>
            <span v-else colspan="100" class="text-center">
                {{$t('nothing_found')}}
            </span>
        </m-flex>
        <div v-else-if="vm.length" class="grid file-list p-3 alt-content-bg">
            <div v-for="file of vm" :key="file.id ?? file.created_at" class="file-item" @click.prevent>
                <m-img class="file-thumbnail" height="200" loading="lazy" :src="getFileThumb(file)" :url-prefix="urlPrefix"/>
                <m-flex class="file-options">
                    <div v-if="file.progress" class="file-progress" :style="{width: file.progress + '%'}"/>
                    <m-flex column class="file-buttons">
                        <span v-if="paused" class="self-center">{{pausedReason ?? $t('waiting')}}</span>
                        <span v-if="file.progress" class="self-center">{{$t('uploading', [file.progress])}}</span>
                        <span v-else-if="file.created_at" class="self-center">{{fullDate(file.created_at)}}</span>
                        <span class="self-center">{{friendlySize(file.size)}}</span>
                        <slot name="buttons" :file="file"/>
                        <m-button @click="removeFileDialog(file)"><i-mdi-delete/> {{$t('delete')}}</m-button>
                    </m-flex>
                </m-flex>
            </div>
        </div>
    </m-flex>
</template>

<script setup lang="ts">
import { friendlySize, fullDate } from '~~/utils/helpers';
import type { File as MWSFile, PendingFileResponse } from '~~/types/models';
import axios, { AxiosError, CanceledError } from 'axios';
import { useI18n } from 'vue-i18n';
import { remove } from '@antfu/utils';
import type { UploadFile } from '~/types/core';

const emit = defineEmits([
    'file-begin',
    'file-uploaded',
    'file-deleted',
]);

const props = defineProps<{
    list?: boolean,
    disabled?: boolean,
    paused?: boolean,
    pausedReason?: string,
    url: string,
    uploadUrl: string,
    urlPrefix?: string,
    useFileAsThumb?: boolean,
    name: string,
    extensions?: string[],
    maxFileSize?: number|string,
    maxSize: number|string,
    maxFiles?: number|string,
    askBeforeRemove?: boolean,
     // Uploads a file in 3 stages: get url -> upload -> confirm completion
     // For this to work, you need to have something like this: files/get-upload-url
     /**
      * Uploads a file in 3 stages: get url -> upload -> confirm completion
      * For this to work, you need to have something like this: files/get-upload-url
      * That should return { id: number, url }
      */
    threeStageUpload?: boolean
}>();

const { showToast } = useToaster();
const { t } = useI18n();
const showErrorToast = useQuickErrorToast();
const { public: runtimeConfig } = useRuntimeConfig();
const yesNoModal = useYesNoModal();

const vm = defineModel<UploadFile[]>({ default: [] }) ;
const uploadingFiles = ref<UploadFile[]>([]);

function getFileThumb(file: UploadFile) {
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
        'upload-area-disabled': reachedMaxFiles.value || props.disabled
    };
});

const input = ref();
const reachedMaxFiles = computed<boolean>(() => {
    if (!props.maxFiles) {
        return false;
    }
    return vm.value.length >= (typeof(props.maxFiles) == "string" ? parseInt(props.maxFiles) : props.maxFiles);
});
const usedSize = computed(() => vm.value.reduce((prev, curr) => prev + curr.size, 0));

const maxFileSizeBytes = computed(() => parseInt((props.maxFileSize || props.maxSize) as string));
const maxSizeBytes = computed(() => parseInt(props.maxSize as string));

watch(() => props.paused, uploadWaitingFiles);

function removeFile(file: UploadFile) {
    for (const [k, f] of Object.entries(vm.value)) {
        if (toRaw(f) == toRaw(file)) {
            vm.value.splice(parseInt(k), 1);
        }
    }
}

/**
 * Handles the actual upload of the file(s)
 */
function register(files: FileList|null) {
    if (!files) {
        return;
    }

    for (const file of files) {
        if (maxFileSizeBytes.value && file.size > maxFileSizeBytes.value) {
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
                id: 0,
                name: file.name,
                size: file.size,
                downloads: 0,
                display_order: 0,
                thumbnail: '/assets/no-preview.webp',
                file: '',
                type: '',
                waiting: true,
                actualFile: file
            };

            uploadingFiles.value.push(insertFile);
    
           //Read the file and get blob src
            const reader = new FileReader();
            reader.addEventListener('load', () => {
                const refFile = vm.value.find(file => toRaw(file) == insertFile);
                if (refFile) {
                    refFile.thumbnail = reader.result as string;
                }
                emit('file-begin', insertFile);
            });
            reader.readAsDataURL(file);
        }
    }
    input.value.value = null;
    
    uploadWaitingFiles();
}

async function uploadWaitingFiles() {
    if (props.paused) {
        return;
    }

    for (const uploadFile of uploadingFiles.value) {
        if (props.threeStageUpload) {
            startThreeStageUpload(uploadFile);
        } else {
            startUpload(uploadFile);
        }
    }
}

async function startUpload(uploadFile: UploadFile) {
    if (!uploadFile.actualFile) {
        return;
    }

    const formData = new FormData();
    formData.append('file', uploadFile.actualFile);

    try {
        const data = await postRequest<MWSFile>(props.uploadUrl, formData, {
            baseURL: runtimeConfig.uploadUrl,
            headers: {'Content-Type': 'multipart/form-data'},
            onUploadProgress: function(progressEvent) {
                if (progressEvent.progress) {
                    uploadFile.progress = Math.round(100 * progressEvent.progress);
                }
            },
            cancelToken: new axios.CancelToken(c => uploadFile.cancel = c)
        });
        
        Object.assign(uploadFile, data);
        uploadFile.cancel = undefined;
        uploadFile.progress = undefined;
        uploadFile.waiting = false;

        remove(uploadingFiles.value, uploadFile);
        vm.value.unshift(uploadFile);

        emit('file-uploaded', uploadFile);
    } catch (e) {
        if (e instanceof AxiosError && !(e instanceof CanceledError)) {
            input.value.value = null;
            removeFile(uploadFile);
            showErrorToast(e, {}, t('failed_upload'));
        }
    }
}

async function startThreeStageUpload(uploadFile: UploadFile) {
    if (!uploadFile.actualFile) {
        return;
    }

    try {
        const file = uploadFile.actualFile;
        const data = await postRequest<PendingFileResponse>(`${props.uploadUrl}/begin-pending`, {
            name: file.name,
            size: file.size,
            type: file.name.split('.').slice(1).join('.')
        });

        await axios.put(data.url, file, {
            headers: data.headers,
            onUploadProgress: function(progressEvent) {
                if (progressEvent.progress) {
                    uploadFile.progress = Math.round(100 * progressEvent.progress);
                }
            },
            cancelToken: new axios.CancelToken(c => uploadFile.cancel = c)
        });

        const fileData = await postRequest(`pending-files/${data.id}/complete`);

        Object.assign(uploadFile, fileData);
        uploadFile.cancel = undefined;
        uploadFile.progress = undefined;
        uploadFile.waiting = false;

        remove(uploadingFiles.value, uploadFile);
        vm.value.unshift(uploadFile);

        emit('file-uploaded', uploadFile);
    } catch (e) {
        if (e instanceof AxiosError && !(e instanceof CanceledError)) {
            input.value.value = null;
            removeFile(uploadFile);
            showErrorToast(e, {}, t('failed_upload'));
        }
    }
}


/**
 * Handles removing files
 */
async function removeFileDialog(file: UploadFile) {
    if (props.askBeforeRemove) {
        yesNoModal({
            title: t('are_you_sure'),
            desc: t('delete_file_desc'),
            yes: async () => await handleRemove(file)
        });
    } else {
        await handleRemove(file);
    }
}

async function handleRemove(file: UploadFile) {
    if (file.cancel) {
        file.cancel('cancelled');
    } else if (file.id) {
        await deleteRequest(`${props.url}/${file.id}`);
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