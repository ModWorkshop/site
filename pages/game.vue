<template>
    <page-block v-if="game">
        <flex>
            <a-button v-if="canEdit" :to="`/admin/game/${game.id}`" icon="cog">{{$t('section_settings')}}</a-button>
            <a-button>{{$t('follow')}}</a-button>
        </flex>
        <div class="content-block">
            <a-banner :src="game.banner" url-prefix="games/banners" style="height: 265px">
                <strong v-if="!game.banner" style="font-size: 2rem;" class="ml-2 align-self-end">
                    {{game.name}}
                </strong>
            </a-banner>
            <flex class="nav p-5" gap="4">
                <NuxtLink :to="`/g/${game.short_name}`">Home</NuxtLink>
                <NuxtLink :to="`/g/${game.short_name}/forum`">Forum</NuxtLink>
                <NuxtLink v-for="button in buttons" :key="button[0]" class="nav-item" href="{{button[1]}}">{{button[0]}}</NuxtLink>
            </flex>
        </div>
        <mod-list :forced-game="game.id"/>
    </page-block>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { Game } from '~~/types/models';

const route = useRoute();
const { data: game, error } = await useFetchData<Game>(`http://localhost:8000/games/${route.params.id}`);

const { hasPermission } = useStore();

useHandleError(error.value, {
    404: 'This game does not exist!'
});

const canEdit = computed(() => hasPermission('edit-mod'));

// const { data: lastThreads } = await useFetchMany<Thread>(`threads?forum_id=${game.value.forum.id}`);

const buttons = computed(() => {
    const btns = game.value.buttons.split(',');
    const res = [];

    for (const btn of btns) {
        res.push([...btn.split('|')]);
    }

    return res;
});

</script>