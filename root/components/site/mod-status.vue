<template>
    <span v-if="statusIcon">
        <a-icon :icon="statusIcon" :class="statusColor" :title="statusText"/>
    </span>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { Mod } from '~~/types/models';
import MdiCancel from '~icons/mdi/cancel';
import MdiCloseThick from '~icons/mdi/close-thick';
import MdiClock from '~icons/mdi/clock';
import MdiAlertCircle from '~icons/mdi/alert-circle';
import MdiEyeOff from '~icons/mdi/eye-off';
import MdiNewspaperRemove from '~icons/mdi/newspaper-remove';



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
        return MdiCancel;
    } else if (mod.approved === false) {
        return MdiCloseThick;
    } else if (mod.approved === null) {
        return MdiClock;
    } else if (!mod.has_download) {
        return MdiAlertCircle;
    } else if (mod.visibility != 'public') {
        return MdiEyeOff;
    } else if (!mod.published_at) {
        return MdiNewspaperRemove;
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