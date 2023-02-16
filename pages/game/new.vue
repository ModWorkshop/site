<template>
    <page-block :game="game" size="xs">
        <content-block>
            <admin-edit-game :game="game"/>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

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