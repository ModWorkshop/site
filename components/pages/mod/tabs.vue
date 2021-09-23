<template>
    <flex grow>
        <tabs class="content-block flex-grow">
            <tab name="description" :title="$t('description')">
                <markdown :text="mod.desc"/>
            </tab>
            <tab name="images" :title="$t('images')" v-if="mod.images.length > 0" style="width: 100%; margin: 0 auto; text-align: center">
                <a v-for="(image, i) of mod.images" :key="image.id" @click="showImage(i)" class="cursor-pointer mb-1 inline-block overflow-hidden">
                    <img :src="`http://localhost:8000/storage/images/${image.file}`" style="max-width:100%;height: 210px;object-fit: cover;"/>
                </a>
                <no-ssr>
                    <vue-easy-lightbox moveDisabled :visible="galleryVisible" :imgs="images" @hide="galleryVisible = false" :index="imageIndex"/>
                </no-ssr>
            </tab>
            <tab name="downloads" :title="$t('downloads')">Nothing for now!</tab>
            <tab v-if="mod.changelog" name="changelog" :title="$t('changelog')">
                <markdown :text="mod.changelog"/>
            </tab>
            <tab v-if="mod.license" name="license" :title="$t('license')">
                <markdown :text="mod.license"/>
            </tab>
            <tab name="instructions" :title="$t('instructions')">Nothing for now!</tab>
        </tabs>
    </flex>
</template>

<script setup>
    import { computed } from '@nuxtjs/composition-api';

    const props = defineProps({
        mod: Object
    });

    let imageIndex = $ref(0);
    let galleryVisible = $ref(false);

    function showImage(nextIndex) {
        imageIndex = nextIndex;
        galleryVisible = true;
    }

    const images = computed(() => {
        const images = [];
        for (const image of props.mod.images) {
            images.push(`http://localhost:8000/storage/images/${image.file}`);
        }
        return images;
    });
</script>