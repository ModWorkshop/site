<template>
    <flex grow>
        <a-tabs class="content-block p-2 flex-grow">
            <a-tab name="description" :title="$t('description')">
                <markdown :text="mod.desc"/>
            </a-tab>
            <a-tab v-if="mod.images.length > 0" name="images" :title="$t('images')" style="width: 100%; margin: 0 auto; text-align: center">
                <a v-for="(image, i) of mod.images" :key="image.id" class="cursor-pointer mb-1 inline-block overflow-hidden" @click="showImage(i)">
                    <img :src="`http://localhost:8000/storage/mods/images/${image.file}`" style="max-width:100%;height: 210px;object-fit: cover;">
                </a>
                <vue-easy-lightbox move-disabled :visible="galleryVisible" :imgs="images" :index="imageIndex" @hide="galleryVisible = false"/>
            </a-tab>
            <a-tab name="downloads" :title="$t('downloads')">Nothing for now!</a-tab>
            <a-tab v-if="mod.changelog" name="changelog" :title="$t('changelog')">
                <markdown :text="mod.changelog"/>
            </a-tab>
            <a-tab v-if="mod.license" name="license" :title="$t('license')">
                <markdown :text="mod.license"/>
            </a-tab>
            <a-tab name="instructions" :title="$t('instructions')">Nothing for now!</a-tab>
        </a-tabs>
    </flex>
</template>

<script setup>
    const props = defineProps({
        mod: Object
    });

    const imageIndex = ref(0);
    const galleryVisible = ref(false);

    function showImage(nextIndex) {
        imageIndex.value = nextIndex;
        galleryVisible.value = true;
    }

    const images = computed(() => {
        const images = [];
        for (const image of props.mod.images) {
            images.push(`http://localhost:8000/storage/images/${image.file}`);
        }
        return images;
    });
</script>