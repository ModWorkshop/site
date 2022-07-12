<template>
    <flex column gap="4">
        <a-input label="Version" v-model="mod.version"/>
        <md-editor label="Changelog" v-model="mod.changelog" rows="12"/>
        <a-select label="Primary Download" desc="If your mod is primarily a single download, you may choose the primary file or link the mod uses" v-model="mod.download_id" placeholder="Select file or link" clearable :options="fileList"/>
        <uploader list name="files" :url="uploadLink" :files="fileList">
            <template #headers>
                <td class="text-center">Primary</td>
            </template>
            <template #rows="{file}">
                <td class="text-center">
                    <input type="radio" v-model="mod.download_id" :value="file.id" @change="mod.download_type = 'file'">
                </td>
            </template>
            <template #buttons="{file}">
                <span class="file-button cursor-pointer" @click.prevent="editFile(file)">
                    <font-awesome-icon icon="cog"/>
                </span>
            </template>
        </uploader>
    </flex>
</template>

<script setup>
    const { mod } = defineProps({
        mod: Object
    });

    const uploadLink = computed(() => mod.value !== null ? `mods/${mod.id}/files`: '');
    const fileList = ref([]);

    function editFile(file) {
    }

    watch(() => mod.files, function() {
        mod.files.forEach(file => {
            fileList.value.push({
                id: file.id,
                date: file.created_at,
                size: file.size,
                name: file.name
            });
        });
    }, {immediate: true});
</script>
<style scoped>

</style>