<template>
    <a-list 
        query 
        :new-button="`${url}/new`"
        :url="`games/${gameId}/instructs-templates`" 
        :item-link="item => `${url}/${item.id}`" 
    />
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-instructions', props.game);

const route = useRoute();
const gameId = computed(() => route.params.game);
const url = computed(() => getAdminUrl('instructs-templates', props.game));
</script>