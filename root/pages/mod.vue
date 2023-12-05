<template>
    <mod-page v-if="(creatingMod && $route.name == 'mod-mod-edit') || $route.params.mod == 'new'" :mod="newMod">
        <m-flex column gap="3">
            <edit-mod-page v-model:mod="newMod"/>
        </m-flex>
    </mod-page>
    <NuxtPage v-else/>
</template>

<script setup lang="ts">
import type { Game, Mod } from '~/types/models';
import { useStore } from '~~/store/index';
import { useRouteParams } from '@vueuse/router';

const store = useStore();
const route = useRoute();

const creatingMod = ref(route.params.mod == 'new');

const mod = useRouteParams<string>('mod');
const gameId = useRouteQuery('game', 0);

const { data: game } = await useFetchData<Game>(`games/${gameId.value}`, { immediate: gameId.value && creatingMod.value });

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
    game: game.value ?? undefined,
    game_id: gameId.value ?? 0,
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

watch(mod, val => {
    creatingMod.value = val == 'new' || parseInt(mod.value) == newMod.value.id;
}, { immediate: true });

</script>