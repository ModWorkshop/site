<template>
    <span v-if="statusIcon">
        <a-icon :icon="statusIcon" :class="statusColor" :title="statusText"/>
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
    const mod = props.mod;
    let str: string|undefined;

    if (mod.suspended) {
        str = 'suspended';
    } else if (mod.approved === null) {
        str = 'mod_waiting';
    } else if (mod.approved === false) {
        str = 'mod_rejected';
    } else if (!mod.has_download) {
        str = 'no_downloads';
    }  else if (mod.visibility == 'public' && !mod.published_at) {
        str = 'not_published';
    } else if (mod.visibility != 'public') {
        str = mod.visibility;
    }

    return str ? t(str) : null;
});

const statusIcon = computed(() => {
    const mod = props.mod;
    if (mod.suspended) {
        return 'mdi:cancel';
    } else if (mod.approved === false) {
        return 'mdi:close-thick';
    } else if (mod.approved === null) {
        return 'mdi:clock';
    } else if (!mod.has_download) {
        return 'mdi:alert-circle';
    } else if (mod.visibility != 'public') {
        return 'mdi:eye-off';
    } else if (!mod.published_at) {
        return 'mdi:newspaper-remove';
    }
});

const statusColor = computed(() => {
    const mod = props.mod;

    if (mod.suspended || mod.approved === false) {
        return 'text-danger';
    } else if (!mod.has_download || !mod.published_at) {
        return 'text-warning';
    } else {
        return 'text-secondary';
    }
});
</script>