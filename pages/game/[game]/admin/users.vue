<template>
    <a-list :url="`games/${game.short_name}/users`" query :item-link="item => `/g/${game.short_name}/user/${item.id}/edit`">
        <template #item="{ item }">
            <a-list-item :to="`/g/${game.short_name}/user/${item.id}`">
                <a-user :user="item" static show-at/>
            </a-list-item>
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-users', props.game);
</script>