<template>
    <span>
        <i v-if="mod.suspended || mod.file_status == 0" :class="`ri-spam-fill text-${mod.suspended ? 'danger' : 'warning'}`" :title="statusText"></i>
        <i v-else-if="mod.file_status == 2" class="ri-time-fill text-secondary" :title="statusText"></i>
        <i v-else-if="mod.visibility != 0 || mod.file_status == 0" class="ri-eye-off-fill text-secondary" :title="statusText"></i>
    </span>
</template>
<script setup lang="ts">
import { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod
}>();

const statusText = computed(() => {
    if (props.mod.suspended) {
        return props.mod.suspended ? 'Sus' : 'No files';
    } else if (props.mod.file_status == 2) {
        return 'Files waiting for approval';
    } else {
        switch (props.mod.visibility) {
            case 1:
                return 'Hidden';
            case 2:
                return 'Unlisted';
            case 4:
                return 'Invite Only';
        }
    }
    return null;
});
</script>