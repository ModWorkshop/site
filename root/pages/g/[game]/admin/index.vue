<template>
    <div>
        <h3>{{ $t('game_admin_page_desc') }}</h3>
    </div>
</template>

<script setup lang="ts">
import type { Game } from '~~/types/models';

defineProps<{
    game: Game
}>();
</script>