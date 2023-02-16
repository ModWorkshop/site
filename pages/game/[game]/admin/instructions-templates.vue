<template>
    <a-list 
        query 
        :new-button="`/admin/games/${gameId}/instructions-templates/new`"
        :url="`games/${gameId}/instructs-templates`" 
        :item-link="item => `/admin/games/${gameId}/instructions-templates/${item.id}`" 
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
</script>