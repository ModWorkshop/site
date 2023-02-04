<template>
    <span>
        <a-icon v-if="mod.suspended" icon="mdi:cancel" class="text-danger" :title="statusText"/>
        <a-icon v-else-if="mod.approved === false" icon="mdi:alert" class="text-danger" :title="statusText"/>
        <a-icon v-else-if="mod.approved === null" icon="mdi:clock" class="text-secondary" :title="statusText"/>
        <a-icon v-else-if="!mod.has_download" icon="mdi:alert-circle" class="text-warning" :title="statusText"/>
        <a-icon v-else-if="mod.visibility == 2 || mod.visibility == 3" icon="mdi:eye-off" class="text-secondary" :title="statusText"/>
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
    } else if (props.mod.approved === null) {
        str = 'mod_waiting';
    } else if (props.mod.approved === false) {
        str = 'mod_rejected';
    } else if (!props.mod.has_download) {
        str = 'no_downloads';
    } else if (props.mod.visibility == 2) {
        str = 'hidden';
    } else if (props.mod.visibility == 3) {
        str = 'unlisted';
    }
    return str ? t(str) : null;
});
</script>