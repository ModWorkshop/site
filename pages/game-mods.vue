<template>
    <page-block v-if="game">
        <the-breadcrumb :items="breadcrumb"/>
        <mod-list :forced-game="game.id"/>
    </page-block>
</template>
<script setup lang="ts">
import { Game } from '~~/types/models';

const route = useRoute();
const { data: game, error } = await useFetchData<Game>(`games/${route.params.id}`);

useHandleError(error, {
    404: 'This game does not exist!'
});

const breadcrumb = computed(() => {
    return [
        { name: game.value.name, id: game.value.short_name, type: 'game' },
        { name: 'Mods' }
    ];
});

</script>