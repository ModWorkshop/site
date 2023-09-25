<template>
    <the-comments
        :url="`users/${user.id}/comments`"
        :show-buttons="false"
        :show-pins="false"
        :can-edit-all="canEditComments"
        :can-delete-all="canEditComments"
        :can-pin="false"
        :can-comment="false"
    />
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import { User } from '~/types/models';

const { user } = defineProps<{
    user: User;
}>();

const { hasPermission, user: me } = useStore();
const canEditComments = computed(() => hasPermission('manage-discussions'));

if (user.id != me?.id) {
    useNeedsPermission('manage-discussions');
}
</script>