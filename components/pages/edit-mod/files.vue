<template>
    <flex column gap="4">
        <group label="Version">
            <el-input v-model="mod.version"/>
        </group>
        <group label="Changelog">
            <md-editor v-model="mod.changelog" rows="12"/>
        </group>
    </flex>
</template>

<script setup>
    import { computed } from "@vue/composition-api";

    const props = defineProps({
        modData: Object
    });
    const mod = computed(() => props.modData);
</script>
<style scoped>

</style>