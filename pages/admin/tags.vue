<template>
    <a-list url="tags" query :item-link="item => `${url}/${item.id}`" :new-button="`${url}/new`" :params="{game_id: game.id}">
        <template #default="{ item }">
            <div>
                <a-tag :color="item.color">{{item.name}}</a-tag> 
            </div>
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-tags', props.game);

const route = useRoute();

const url = computed(() => route.params.game ? `/admin/games/${route.params.game}/tags` : '/admin/tags');
</script>