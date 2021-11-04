<template>
    <flex column gap="4">
        <group label="Version">
            <el-input v-model="mod.version"/>
        </group>
        <group label="Changelog">
            <md-editor v-model="mod.changelog" rows="12"/>
        </group>
        <group label="Primary Download" desc="If your mod is primarily a single download, you may choose the primary file or link the mod uses">
            <el-select v-model="mod.download_id" placeholder="Select file or link" clearable filterable>
                <el-option v-for="file in fileList" :key="file.id" :label="file.name" :value="file.id"/>
            </el-select>
        </group>
        <uploader list name="files" :url="uploadLink" :files="fileList">
            <template #headers>
                <td align="center">Primary</td>
            </template>
            <template #rows="{file}">
                <td align="center">
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
    import { ref, computed, watch } from '@nuxtjs/composition-api';

    const props = defineProps({
        modData: Object
    });

    const mod = computed(() => props.modData);

    const uploadLink = computed(() => mod.value !== null ? `mods/${mod.value.id}/files`: '');
    const fileList = ref([]);

    function editFile(file) {
    }

    watch(() => mod.value.files, function() {
        mod.value.files.forEach(file => {
            fileList.push({
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