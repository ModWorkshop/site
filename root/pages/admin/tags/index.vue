<template>
    <m-list url="tags" query :item-link="item => `${url}/${item.id}`" :new-button="`${url}/new`" :params="{game_id: game?.id, global: !game}">
        <template #item-name="{ item }">
            <m-tag :color="item.color">{{item.name}}</m-tag> 
        </template>
    </m-list>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-tags', props.game);

const url = computed(() => getAdminUrl('tags', props.game));
</script>