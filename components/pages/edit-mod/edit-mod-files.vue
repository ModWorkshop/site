<template>
    <flex column gap="4">
        <a-input v-model="mod.version" label="Version"/>
        <md-editor v-model="mod.changelog" label="Changelog" rows="12"/>
        <a-select v-model="mod.download_id" label="Primary Download" desc="If your mod is primarily a single download, you may choose the primary file or link the mod uses" placeholder="Select file or link" clearable :options="fileList"/>
        <uploader list name="files" :url="uploadLink" :files="fileList">
            <template #headers>
                <td class="text-center">Primary</td>
            </template>
            <template #rows="{file}">
                <td class="text-center">
                    <input v-model="mod.download_id" type="radio" :value="file.id" @change="mod.download_type = 'file'">
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

<script setup lang="ts">
    import { File, Mod } from '~~/types/models';

    const props = defineProps<{
        mod: Mod
    }>();

    const uploadLink = computed(() => props.mod !== null ? `mods/${props.mod.id}/files`: '');
    const fileList = ref([]);

    function editFile(file: File) {
        //TODO
    }

    watch(() => props.mod.files, function() {
        props.mod.files.forEach(file => {
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