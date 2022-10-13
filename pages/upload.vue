<template>
    <page-block size="sm" :game="game" :breadcrumb="game ? breadcrumb : null">
        <Title>{{$t('upload_mod')}}</Title>
        <a-form :model="mod" :created="false" @submit="create">
            <content-block>
                <h1>
                    Upload Mod
                </h1>
                <h4 class="whitespace-pre">{{$t('mod_creation_desc')}}</h4>
    
                <a-input v-model="mod.name" label="Name" maxlength="150" minlength="3" required desc="Maximum of 150 letters and minimum of 3 letters"/>
                <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

                <a-select v-if="!gameName" v-model="mod.game_id" label="Game" placeholder="Select a game" :options="games.data">
                    <template #option="{ option }">
                        <a-simple-game :game="option"/>
                    </template>
                    <template #list-option="{ option }">
                        <a-simple-game :game="option"/>
                    </template>
                </a-select>

                <a-input v-if="categories" :label="$t('category')">
                    <category-tree v-model="mod.category_id" style="height: 200px;" class="input-bg p-2 overflow-y-scroll" :categories="categories.data"/>
                </a-input>

                <flex class="mx-auto">
                    <a-button type="submit" :disabled="!mod.name || !mod.desc">Create</a-button>
                </flex>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
import { Ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Category, Game, Mod } from '~~/types/models';

definePageMeta({
    middleware: 'unbanned-users-only'
});

const store = useStore();
const router = useRouter();
const route = useRoute();
const { t } = useI18n();

const mod: Ref<Mod> = ref({
    id: -1,
    name: '',
    desc: '',
    images: [],
    files: [],
    links: [],
    members: [],
    short_desc: '',
    changelog: '',
    license: '',
    instructions: '',
    donation: '',
    legacy_banner_url: '',
    game_id: null,
    version: '',
    user_id: store.user.id,
    user: store.user,
    downloads: 0,
    likes: 0,
    views: 0,
    visibility: 1,
    suspended: false,
    comments_disabled: false,
    approved: false,
    has_download: false,
});

const gameName = computed(() => route.params.gameId);

const { data: games } = await useFetchMany<Game>('games', { immediate: !gameName.value });
const { data: game } = await useResource<Game>('game', 'games');

store.currentGame = game.value;

useUnbannedOnly();

const breadcrumb = computed(() => {
    if (game.value) {
        return [
            { name: t('games'), to: 'games' },
            { name: game.value.name, to: `g/${gameName.value}` },
            { name: t('upload') }
        ];
    }
});

const gameId = computed(() => game.value ? game.value.id : mod.value.game_id);

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories?include_paths=1`, {
    immediate: !!gameId.value
});

watch(() => mod.value.game_id, val => {
    if (val) {
        refetchCats();
    }
});

async function create() {
    try {
        const fetchedMod = await usePost<Mod>(`games/${gameId.value}/mods`, mod.value);
        router.push(`/mod/${fetchedMod.id}/edit`);
    } catch (error) {
        console.error(error);
        return;
    }
}
</script>