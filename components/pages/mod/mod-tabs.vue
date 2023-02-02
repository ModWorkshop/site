<template>
    <flex grow>
        <a-tabs class="content-block p-2 flex-grow" query>
            <a-tab name="description" :title="$t('description')">
                <a-markdown :text="mod.desc"/>
            </a-tab>
            <a-tab v-if="mod.images.length > 0" name="images" :title="$t('images')" :column="false" wrap gap="2">
                <a-img 
                    v-for="(image, i) of mod.images"
                    :key="image.id" 
                    class="cursor-pointer"
                    url-prefix="mods/images"
                    :src="`${(image.has_thumb ? config.thumbnail_prefix : '') + image.file}`" 
                    style="max-height: 200px;" 
                    @click="showImage(i)"
                />
                <ClientOnly>
                    <vue-easy-lightbox move-disabled :visible="galleryVisible" :imgs="images" :index="imageIndex" @hide="galleryVisible = false"/>
                </ClientOnly>
            </a-tab>
            <a-tab name="downloads" :title="$t('downloads')">
                <div v-for="labeled of labeledFiles" :key="labeled.label" class="flex-grow">
                    <h2 v-if="labeled.label != 'all'">{{labeled.label}}</h2>
                    <flex column gap="2">
                        <flex v-for="file of labeled.files" :key="file.id" gap="3" wrap class="alt-bg-color p-3 items-center place-content-center">
                            <div class="mr-2">
                                <a-img src="https://modworkshop.net/mydownloads/previews/fileimages/file_dl_big.png" width="128" height="128"/>
                            </div>
                            <flex grow column>
                                <h3>{{file.name}}</h3>
                                <span v-if="file.version">
                                    <a-icon icon="tag" :title="$t('version')"/> {{file.version}}
                                </span>
                                <flex class="items-center">
                                    <a-icon icon="clock" :title="$t('upload_date')"/>
                                    <i18n-t keypath="by_user_time_ago">
                                        <template #time>
                                            <time-ago :time="file.created_at"/>
                                        </template>
                                        <template #user>
                                            <a-user :user="file.user" avatar-size="xs"/>
                                        </template>
                                    </i18n-t>
                                </flex>
                                <a-markdown v-if="file.desc" class="mt-3" :text="file.desc"/>
                            </flex>
                            <div>
                                <a-button v-if="file.size" class="text-xl text-center" :to="`${modUrl}/download/${file.id}`" icon="download">
                                    {{$t('download')}}
                                    <small class="mt-2 text-center block">{{file.type}} - {{friendlySize(file.size)}}</small>
                                </a-button>
                                <VDropdown v-else>
                                    <a-button class="large-button w-full text-center" icon="download">
                                        {{$t('show_download_link')}}
                                    </a-button>
                                    <template #popper>
                                        <div class="p-2">
                                            {{$t('show_download_link_warn')}}
                                            <br>
                                            <a :href="file.url">{{file.url}}</a>
                                        </div>
                                    </template>
                                </VDropdown>
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
            <a-tab v-if="dependencies.length || mod.instructions" name="instructions" :title="$t('instructions_tab')" gap="0">
                <div v-if="dependencies.length">
                    <h2>{{$t('dependencies')}}</h2>
                    <ol style="padding-inline-start: 32px;">
                        <li v-for="dep in dependencies" :key="dep.id" class="mb-1 align-middle">
                            <flex gap="2" inline class="items-center align-middle">
                                <NuxtLink :to="dep.mod ? `/mod/${dep.mod_id}` : dep.url">
                                    <mod-thumbnail :thumbnail="dep.mod?.thumbnail" style="height: 64px;"/>
                                </NuxtLink>
                                <flex column>
                                    <template v-if="dep.mod">
                                        <NuxtLink :to="`/mod/${dep.mod_id}`">
                                            {{dep.mod.name}}
                                        </NuxtLink>
                                        <a-user avatar-size="sm" :user="dep.mod.user"/>
                                    </template>
                                    <template v-else>
                                        <NuxtLink :to="dep.url">
                                            {{dep.name}} <a-tag v-if="dep.optional">{{$t('optional')}}</a-tag>
                                        </NuxtLink>
                                        <span>
                                            {{$t('offsite_mod')}}
                                        </span>
                                    </template>
                                </flex>
                            </flex>
                        </li>
                    </ol>
                </div>
                <div v-if="mod.instructions">
                    <h2>{{$t('instructions')}}</h2>
                    <a-markdown class="mb-3" :text="(mod.instructs_template ? mod.instructs_template.instructions + '\n' : '') + mod.instructions"/>
                </div>
            </a-tab>
        </a-tabs>
    </flex>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';
import { friendlySize } from '~~/utils/helpers';

const props = defineProps<{
    mod: Mod
}>();

const { public: config } = useRuntimeConfig();

const dependencies = computed(() => {
    const deps = props.mod.dependencies ?? [];
    const templateDeps = props.mod.instructs_template?.dependencies ?? [];

    const combinedDeps = [...deps, ...templateDeps];

    return combinedDeps.sort((a,b) => a.order - b.order);
});

const modUrl = computed(() => `/mod/${props.mod.id}`);

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
        images.push(`${config.apiUrl}/storage/mods/images/${image.file}`);
    }
    return images;
});
</script>
