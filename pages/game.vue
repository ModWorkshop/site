<template>
    <page-block>
        <the-breadcrumb :items="breadcrumb"/>

        <flex>
            <a-button to="" icon="cog">{{$t('game_profile')}}</a-button>
            <a-button :icon="game.followed ? 'minus' : 'plus'" @click="setFollowGame(game)">{{$t(game.followed ? 'unfollow' : 'follow')}}</a-button>
        </flex>
        <div class="content-block">
            <a-banner :src="game.banner" url-prefix="games/banners" style="height: 265px">
                <strong v-if="!game.banner" style="font-size: 2rem;" class="ml-2 align-self-end">
                    {{game.name}}
                </strong>
            </a-banner>
            <flex class="nav p-5" gap="4">
                <a-link-button :to="`/g/${game.short_name}`">Home</a-link-button>
                <a-link-button v-if="!store.user || !store.isBanned" :to="`/g/${game.short_name}/upload`">{{$t('upload_mod')}}</a-link-button>
                <a-link-button :to="`/g/${game.short_name}/forum`">Forum</a-link-button>

                <a-link-button v-for="button in buttons" :key="button[0]" class="nav-item" :href="button[1]">{{button[0]}}</a-link-button>
            </flex>
        </div>
        <mod-list :forced-game="game.id"/>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Game } from '~~/types/models';
import { setFollowGame } from '~~/utils/follow-helpers';
const store = useStore();
const { t } = useI18n();

const { data: game } = await useResource<Game>('game', 'games');

const breadcrumb = computed(() => {
    if (game.value) {
        return [
            { name: t('games'), to: 'games' },
            { name: game.value.name },
        ];
    }
});

store.currentGame = game.value;

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