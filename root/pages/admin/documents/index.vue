<template>
    <a-list
        :url="apiUrl"
        query
        :item-link="item => `${url}/${item.id}`"
        :new-button="`${url}/new`"
        :params="{ game_id: game?.id, get_unlisted: true }"
    >
        <template #item-buttons="{ item }">
            <a-button @click.prevent="$router.push(`/${pageUrl}/${item.id}`)"><i-mdi-launch/></a-button>
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '../../../types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-documents', props.game);

const url = computed(() => getAdminUrl('documents', props.game));
const apiUrl = computed(() => getGameResourceUrl('documents', props.game));
const pageUrl = computed(() => getGameResourceUrl('document', props.game));
</script>