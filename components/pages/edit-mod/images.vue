<template>
    <div>
        <uploader name="images" :url="uploadLink" :files="fileList"/>
    </div>
</template>

<script setup>
    import { ref, computed, watch } from '@nuxtjs/composition-api';

    const props = defineProps({
        modData: Object
    });
    
    const mod = computed(() => props.modData);
    const uploadLink = computed(() => mod.value !== null ? `mods/${mod.value.id}/images`: '');
    const fileList = ref([]);

    watch(() => mod.value.images, function() {
        mod.value.images.forEach(image => {
            fileList.value.push({
                id: image.id,
                name: image.file,
                url: `http://localhost:8000/storage/images/${image.file}`
            });
        });
    }, {immediate: true});
</script>