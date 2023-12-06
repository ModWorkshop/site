<template>
    <mod-page :mod="newMod" :page-block="!game">
        <m-flex column gap="3">
            <edit-mod-page v-model:mod="newMod"/>
        </m-flex>
    </mod-page>
</template>

<script setup lang="ts">
import type { Game, Mod } from '~/types/models';
import { useStore } from '~~/store/index';

const store = useStore();

const { game } = defineProps<{
    game?: Game
}>();

if (game) {
    store.currentGame = game;
}

const newMod = ref<Mod>({
    id: 0,
    name: '',
    desc: '',
    images: [],
    members: [],
    short_desc: '',
    changelog: '',
    license: '',
    instructions: '',
    donation: '',
    legacy_banner_url: '',
    game_id: game?.id ?? 0,
    version: '',
    user_id: 0,
    user: store.user!,
    downloads: 0,
    likes: 0,
    views: 0,
    visibility: 'public',
    suspended: false,
    comments_disabled: false,
    approved: false,
    has_download: false,
    disable_mod_managers: false
});
</script>