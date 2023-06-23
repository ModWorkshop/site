<template>
    <a-list
        :url="apiUrl"
        query
        :item-link="item => `${url}/${item.id}`"
        :new-button="`${url}/new`"
        :params="{ game_id: game?.id }"
    />
</template>

<script setup lang="ts">
import { Game } from '../../../types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-documents', props.game);

const url = computed(() => getAdminUrl('documents', props.game));
const apiUrl = computed(() => getGameResourceUrl('documents', props.game));
</script>