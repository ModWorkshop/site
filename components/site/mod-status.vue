<template>
    <span>
        <font-awesome-icon v-if="mod.suspended" icon="ban" class="text-danger" :title="statusText"/>
        <font-awesome-icon v-else-if="!mod.approved" icon="clock" class="text-secondary" :title="statusText"/>
        <font-awesome-icon v-else-if="!mod.has_download" icon="circle-exclamation" class="text-warning" :title="statusText"/>
        <font-awesome-icon v-else-if="mod.visibility == 2 || mod.visibility == 3" icon="eye-slash" class="text-secondary" :title="statusText"/>
    </span>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const { t } = useI18n();

const statusText = computed(() => {
    let str: string;
    if (props.mod.suspended) {
        str = 'suspended';
    } else if (!props.mod.approved) {
        str = 'files_waiting';
    } else if (!props.mod.has_download) {
        str = 'no_files';
    } else if (props.mod.visibility == 2) {
        str = 'hidden';
    } else if (props.mod.visibility == 3) {
        str = 'unlisted';
    }
    return str ? t(str) : null;
});
</script>