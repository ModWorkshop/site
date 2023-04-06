<template>
    <a-list :url="`games/${game.id}/users`" query :item-link="item => `/g/${game.id}/user/${item.id}/edit`">
        <template #item="{ item }">
            <a-list-item :to="`/g/${game.id}/user/${item.id}`">
                <a-user :user="item" static show-at/>
            </a-list-item>
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '~/types/models';

defineProps<{
    game: Game
}>();

useNeedsPermission('manage-users');
</script>