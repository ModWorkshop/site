<template>
    <a-list url="mods" query :item-link="item => `/mod/${item.id}/edit`" :params="{ game_id: game?.id }">
        <template #before-item="{ item }">
            <mod-thumbnail :thumbnail="item.thumbnail" style="width: 100px;"/>
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-mods', props.game);
</script>