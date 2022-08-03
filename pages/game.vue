<template>
    <page-block v-if="game">
        <div class="content-block">
            <a-banner v-if="game.banner" :src="game.banner" url-prefix="games/banners" style="height: 265px"/>
            <div v-else class="d-flex card-img-top" style="background-image: url('http://localhost:8000/storage/banners/default_banner.webp');background-position: center;height: 265px;100%;">
                <strong style="font-size: 2rem;" class="ml-2 align-self-end">
                    {{game.name}}
                </strong>
            </div>
            <flex class="nav p-5" gap="4">
                <NuxtLink v-for="button in buttons" :key="button[0]" class="nav-item" href="{{button[1]}}">{{button[0]}}</NuxtLink>
            </flex>
        </div>
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

const buttons = computed(() => {
    const btns = game.value.buttons.split(',');
    const res = [];

    for (const btn of btns) {
        res.push([...btn.split('|')]);
    }

    return res;
});

</script>