<template>
    <page-block :game="game" size="xs">
        <m-content-block>
            <admin-edit-game :game="game"/>
        </m-content-block>
    </page-block>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';
definePageMeta({ alias: '/game/new' });

const game = ref<Game>({
    id: 0,
    forum_id: 0,
    name: '',
    short_name: "",
    thumbnail: "",
    banner: "",
    buttons: "",
    webhook_url: "",
    last_date: "",
    created_at: "",
    updated_at: ""
});
useNeedsPermission('manage-games');
</script>