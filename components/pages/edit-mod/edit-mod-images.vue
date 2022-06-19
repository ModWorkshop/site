<template>
    <div>
        <uploader name="images" :url="uploadLink" :files="fileList">
            <template #buttons="{file}">
                <a-button class="file-button cursor-pointer" icon="image" @click.prevent="() => setThumbnail(file)">
                    Make Thumbnail
                </a-button>
                <a-button class="file-button cursor-pointer" icon="image" @click.prevent="mod.banner_id = file.id">
                    Make Banner
                </a-button>
            </template>
        </uploader>
    </div>
</template>

<script setup>
    const { mod } = defineProps({
        mod: Object
    });
    
    const uploadLink = computed(() => mod ? `mods/${mod.id}/images`: '');
    const fileList = ref([]);

    function setThumbnail(file) {
        mod.thumbnail_id = file.id;
    }

    watch(() => mod.images, function() {
        fileList.value = [];
        mod.images.forEach(image => {
            fileList.value.push({
                id: image.id,
                name: image.file,
                url: `http://localhost:8000/storage/images/${image.file}`
            });
        });
    }, {immediate: true});
</script>