<template>
    <span>
        <span v-if="mod.is_nsfw">🔞</span>
        <i v-if="mod.suspended_status == 1 || mod.file_status == 0" :class="`ri-spam-fill text-${mod.suspended_status == 1 ? 'danger' : 'warning'}`" :title="statusText"></i>
        <i v-else-if="mod.file_status == 2" class="ri-time-fill text-secondary" :title="statusText"></i>
        <i v-else-if="mod.hidden != 0 || mod.file_status == 0" class="ri-eye-off-fill text-secondary" :title="statusText"></i>
    </span>
</template>
<script>
export default {
    props: {
        mod: Object
    },
    computed: {
        statusText() {
            if (this.mod.suspended_status == 1) {
                return this.mod.suspended_status == 1 ? 'Sus' : 'No files';
            } else if (this.mod.file_status == 2) {
                return 'Files waiting for approval';
            } else {
                switch (this.mod.hidden) {
                    case 1:
                        return 'Hidden';
                    case 2:
                        return 'Unlisted';
                    case 4:
                        return 'Invite Only';
                }
            }
            return null;
        }
    }
};
</script>
<style lang="">
    
</style>