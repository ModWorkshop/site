<template>
    <flex grow>
        <a-tabs class="content-block p-2 flex-grow" query>
            <a-tab name="description" :title="$t('description')">
                <a-markdown :text="mod.desc"/>
            </a-tab>
            <a-tab v-if="mod.images && mod.images.length > 0" name="images" :title="$t('images')" :column="false" wrap gap="2">
                <a-img 
                    v-for="(image, i) of mod.images"
                    :key="image.id" 
                    class="mod-image cursor-pointer"
                    url-prefix="mods/images"
                    :src="`${(image.has_thumb ? 'thumbnail_' : '') + image.file}`" 
                    style="max-height: 200px;" 
                    @click="showImage(i)"
                />
                <ClientOnly>
                    <vue-easy-lightbox move-disabled :visible="galleryVisible" :imgs="images" :index="imageIndex" @hide="galleryVisible = false"/>
                </ClientOnly>
            </a-tab>
            <a-tab v-if="mod.has_download" name="downloads" :title="$t('downloads')">
                <mod-downloads-tab :mod="mod"/>
            </a-tab>
            <a-tab v-if="mod.changelog" name="changelog" :title="$t('changelog')">
                <a-markdown :text="mod.changelog"/>
            </a-tab>
            <a-tab v-if="mod.license" name="license" :title="$t('license')">
                <a-markdown :text="mod.license"/>
            </a-tab>
            <a-tab v-if="dependencies.length || instructions" name="instructions" :title="$t('instructions_tab')" gap="0">
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
                <div v-if="instructions">
                    <h2>{{$t('instructions')}}</h2>
                    <a-markdown class="mb-3" :text="instructions"/>
                </div>
            </a-tab>
        </a-tabs>
    </flex>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

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

const instructions = computed(() => (props.mod.instructs_template ? props.mod.instructs_template.instructions + '\n' : '') + props.mod.instructions);

const imageIndex = ref(0);
const galleryVisible = ref(false);

function showImage(nextIndex) {
    imageIndex.value = nextIndex;
    galleryVisible.value = true;
}

const images = computed(() => {
    const images: string[] = [];

    if (props.mod.images) {
        for (const image of props.mod.images) {
            images.push(`${config.storageUrl}/mods/images/${image.file}`);
        }
    }

    return images;
});
</script>

<style scoped>
.mod-image {
    object-fit: cover;
}
</style>