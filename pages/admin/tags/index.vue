<template>
    <a-list url="tags" query :item-link="item => `${url}/${item.id}`" :new-button="`${url}/new`" :params="{game_id: game?.id}">
        <template #item-name="{ item }">
            <a-tag :color="item.color">{{item.name}}</a-tag> 
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-tags', props.game);

const url = computed(() => getAdminUrl('tags', props.game));
</script>