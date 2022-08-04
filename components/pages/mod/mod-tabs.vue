<template>
    <flex grow>
        <a-tabs class="content-block p-2 flex-grow">
            <a-tab name="description" :title="$t('description')">
                <a-markdown :text="mod.desc"/>
            </a-tab>
            <a-tab v-if="mod.images.length > 0" name="images" :title="$t('images')" style="width: 100%; margin: 0 auto; text-align: center">
                <a v-for="(image, i) of mod.images" :key="image.id" class="cursor-pointer mb-1 inline-block overflow-hidden" @click="showImage(i)">
                    <a-img :src="`/mods/images/${image.file}`" style="max-width:100%;height: 210px;object-fit: cover;"/>
                </a>
                <vue-easy-lightbox move-disabled :visible="galleryVisible" :imgs="images" :index="imageIndex" @hide="galleryVisible = false"/>
            </a-tab>
            <a-tab name="downloads" :title="$t('downloads')">
                <div v-for="labeled of labeledFiles" :key="labeled.label" class="mt-3">                       
                    <h2 v-if="labeled.label != 'all'">{{labeled.label}}</h2>
                    <flex column gap="2">
                        <flex v-for="file of labeled.files" :key="file.id" class="alt-bg-color p-3 items-center">
                            <div class="mr-2">
                                <a-img src="https://modworkshop.net/mydownloads/previews/fileimages/file_dl_big.png" width="128" height="128"/>
                            </div>
                            <flex grow column>
                                <strong style="font-size: 13px;">{{file.name}}</strong>
                                <span>Version {{file.version}}</span>
                                <flex>
                                    <span class="my-auto">{{timeAgo(file.created_at)}} by</span>
                                    <a-user :user="file.user" avatar-size="xs"/>
                                </flex>
                                <a-markdown v-if="file.desc" class="mt-3" :text="file.desc"/>
                            </flex>
                            <div>
                                <a-button v-if="file.size" :href="`${config.apiUrl}/files/${file.id}/download`" icon="download" download large>
                                    {{$t('download')}}
                                    <small class="mt-2 text-center block">{{file.type}} - {{friendlySize(file.size)}}</small>
                                </a-button>
                                <va-popover v-else trigger="click">
                                    <template #body>
                                        <div style="width: 250px;">
                                            {{$t('show_download_link_warn')}}
                                            <br>
                                            <a :href="file.url">{{file.url}}</a>
                                        </div>
                                    </template>
                                    <a-button class="large-button w-full text-center" icon="download">
                                        {{$t('show_download_link')}}
                                    </a-button>
                                </va-popover>
                            </div>
                        </flex>
                    </flex>
                </div>
            </a-tab>
            <a-tab v-if="mod.changelog" name="changelog" :title="$t('changelog')">
                <a-markdown :text="mod.changelog"/>
            </a-tab>
            <a-tab v-if="mod.license" name="license" :title="$t('license')">
                <a-markdown :text="mod.license"/>
            </a-tab>
            <a-tab name="instructions" :title="$t('instructions')">Nothing for now!</a-tab>
        </a-tabs>
    </flex>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const { public: config } = useRuntimeConfig();

const imageIndex = ref(0);
const galleryVisible = ref(false);

const labeledFiles = computed(() => {
    const sorted = [];
    for (const file of [...props.mod.files, ...props.mod.links]) {
        const label = file.label ?? 'all';
        let labeled = sorted.find(curr => curr.label == label);

        if (!labeled) {
            labeled = { label, files: [] };
            sorted.push(labeled);
        }

        labeled.files.push(file);
    }
    
    return sorted;
});

function showImage(nextIndex) {
    imageIndex.value = nextIndex;
    galleryVisible.value = true;
}

const images = computed(() => {
    const images = [];
    for (const image of props.mod.images) {
        images.push(`${config.apiUrl}/mods/images/${image.file}`);
    }
    return images;
});
</script>