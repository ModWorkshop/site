<template>
    <a-list
        url="documents"
        query
        :item-link="item => `${url}/${item.id}`"
        :new-button="`${url}/new`"
        :params="{ game_id: game.id }"
    />
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-docs', props.game);

const url = computed(() => props.game ? `/admin/games/${props.game.id}/docs` : '/admin/docs');
</script>