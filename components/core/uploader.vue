<template>
    <div class="flex flex-col" gap="3"
        style="width: 100%; min-height: 150px; background-color: var(--bg-color)" 
        @dragover.prevent=""
        @drop.prevent="handleDrop"
    >
        <label :for="`${name}-file-browser-open`">
            <div class="text-center">
                <h2>Drop files here or click the area to upload files</h2>
            </div>
        </label>
        <input :id="`${name}-file-browser-open`" type="file" @change="handleFileBrowser" hidden multiple/>
        <div v-if="list" class="p-3">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Upload Date</th>
                        <th align="center">Actions</th>
                        <slot name="headers"/>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="[i, file] of files.entries()" :key="i">
                        <td>{{file.name}}</td>
                        <td>{{friendlySize(file.size)}}</td>
                        <td v-if="file.date">{{fullDate(file.date)}}</td>
                        <td v-else>Uploading: {{file.progress}}% </td>
                        <td align="center">
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
        <div v-else class="grid file-list p-3 mb-8">
            <div class="file-item" v-for="[i, file] of files.entries()" :key="i" @click.prevent>
                <img class="file-thumbnail" :src="file.url" alt="">
                <flex class="file-options">
                    <div v-if="file.progress != -1" class="file-progress" :style="{width: file.progress + '%'}"/>
                    <span class="file-buttons">
                        <span class="file-button cursor-pointer" @click.prevent="handleRemove(file)">
                            <font-awesome-icon icon="trash"/>
                        </span>
                    </span>
                </flex>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { friendlySize, fullDate } from '../../utils/helpers';
    import { Notification } from 'element-ui';
    import { useContext, computed } from '@nuxtjs/composition-api';

    const props = defineProps({
        list: Boolean,
        buttons: {type: Array, default: () => []},
        url: { required: true, type: String },
        name: { required: true,  type: String },
        files: { required: true, type: Array }
    });

    const { $axios } = useContext();

    const computedFiles = computed(() => props.files);

    /**
     * Handles the actual upload of the file(s)
     */
    async function upload(files) {
        for (const file of files) {
            let insertFile = {
                id: -1,
                name: file.name,
                url: '',
                size: file.size,
                progress: -1,
                cancel: null
            };
            try {
                //Read the file and get blob src
                let reader = new FileReader(file);
                reader.onload = () => {
                    insertFile.url = reader.result;
                    computedFiles.value.push(insertFile);
                };
                reader.readAsDataURL(file);

                const formData = new FormData();
                formData.append('file', file);
                const { data } = await $axios.post(props.url, formData, {
                    headers: {'Content-Type': 'multipart/form-data'},
                    onUploadProgress: function(progressEvent) {
                        insertFile.progress = Math.round(100 * (progressEvent.loaded / progressEvent.total));
                    },
                    cancelToken: new $axios.CancelToken(c => insertFile.cancel = c)
                });

                insertFile.progress = -1;
                Object.assign(insertFile, {
                    id: data.id,
                    name: data.name || data.file,
                    date: data.created_at,
                    size: data.size,
                    url: `http://localhost:8000/storage/images/${data.file}`
                });
            } catch (error) {
                console.log(error);
                if (!$axios.isCancel(error)) {
                    Notification.error({
                        title: 'Error',
                        message: 'File failed to upload: ' + error.message
                    });
                }
                removeFile.call(this, insertFile);
            }
        }
    }

    function removeFile(file) {
        for (const [k, f] of Object.entries(computedFiles.value)) {
            if (f == file) {
                this.$delete(computedFiles.value, k);
            }
        }
    }

    /**
     * Handles removing files
     */
    async function handleRemove(file) {
        if (file.cancel) {
            file.cancel('Cancelled by user');
        } else {
            try {
                await $axios.delete(`${props.url}/${file.id}`);
                removeFile.call(this, file);
            } catch (error) {
                console.log(error);
            }
        }
    }

    /**
     * Handles uploads made by the file browser
     * Since we need to use this.$delete we must also assign 'this' to the upload function. 
     * This will not be required in Nuxt 3.
     */
    async function handleFileBrowser(e) {
        const input = e.target;
        await upload.call(this,input.files);
    }

    /**
     * Handles uploads made by dropping files onto the uploader
     */
    async function handleDrop(e) {
        const files = e.dataTransfer.files;
        upload.call(this, files);
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
        width: 100%;
        height: 100%;
        background-color: rgba(36, 36, 36, 0.5);
        display: none;
    }

    .file-item:hover .file-buttons {
        display: flex;
    }

    .file-button {
        margin: auto;
    }
</style>

<style>
    .file-item img {
        object-fit: cover;
    }
</style>