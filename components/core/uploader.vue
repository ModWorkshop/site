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
                    <tr v-for="[i, file] of files.entries()" :key="i">
                        <td>{{file.name}}</td>
                        <td>{{friendlySize(file.size)}}</td>
                        <td v-if="file.date">{{fullDate(file.date)}}</td>
                        <td v-else>Uploading: {{file.progress}}% </td>
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
            <div v-for="[i, file] of files.entries()" :key="i" class="file-item" @click.prevent>
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

<script setup>
    import { friendlySize, fullDate } from '../../utils/helpers';

    const props = defineProps({
        list: Boolean,
        buttons: {type: Array, default: () => []},
        url: { required: true, type: String },
        name: { required: true,  type: String },
        files: { required: true, type: Array }
    });

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
                const controller = new AbortController();
                const { data } = await usePost(props.url, formData, {
                    // onUploadProgress: function(progressEvent) {
                    //     insertFile.progress = Math.round(100 * (progressEvent.loaded / progressEvent.total));
                    // },
                    signal: controller.signal
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
                    // Notification.error({
                    //     title: 'Error',
                    //     message: 'File failed to upload: ' + error.message
                    // });
                removeFile.call(this, insertFile);
            }
        }
    }

    function removeFile(file) {
        for (const [k, f] of Object.entries(computedFiles.value)) {
            if (f == file) {
                computedFiles.value.splice(k);
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
                await useDelete(`${props.url}/${file.id}`);
                removeFile.call(this, file);
            } catch (error) {
                console.log(error);
            }
        }
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