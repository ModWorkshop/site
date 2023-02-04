<template>
    <page-block size="sm" :game="game" :breadcrumb="game ? breadcrumb : null">
        <Title>{{$t('upload_mod')}}</Title>
        <h1>{{$t('upload_mod')}}</h1>
        <a-form :model="mod" :created="false" @submit="create">
            <content-block>
                <h4>{{$t('mod_creation_desc')}}</h4>
    
                <a-input v-model="mod.name" :label="$t('name')" maxlength="150" minlength="3" required :desc="$t('mod_name_desc')"/>
                <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>
                
                <a-game-select v-if="!gameName" v-model="mod.game_id" :label="$t('game')"/>

                <a-category-select 
                    v-if="categories"
                    v-model="mod.category_id"
                    :max-height="200"
                    :label="$t('category')"
                    :categories="categories.data"
                />

                <flex class="mx-auto">
                    <a-button type="submit" :disabled="!mod.name || !mod.desc">{{$t('upload_mod')}}</a-button>
                </flex>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
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
const showToast = useQuickErrorToast();

const mod: Mod = reactive({
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
    user_id: store.user?.id,
    user: store.user,
    downloads: 0,
    likes: 0,
    views: 0,
    visibility: 'public',
    suspended: false,
    comments_disabled: false,
    approved: false,
    has_download: false,
});

const gameName = computed(() => route.params.gameId);

const { data: game } = await useResource<Game>('game', 'games');

if (game.value) {
    store.currentGame = game.value;
}

useUnbannedOnly();

const breadcrumb = computed(() => {
    if (game.value) {
        return [
            { name: t('games'), to: 'games' },
            { name: game.value.name, to: `g/${gameName.value}` },
            { name: t('upload_mod') }
        ];
    }
});

const gameId = computed(() => game.value ? game.value.id : mod.game_id);

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories?include_paths=1`, {
    immediate: !!gameId.value
});

watch(() => mod.game_id, val => {
    if (val) {
        refetchCats();
    }
});

async function create() {
    try {
        const fetchedMod = await usePost<Mod>(`games/${gameId.value}/mods`, mod);
        router.push(`/mod/${fetchedMod.id}/edit`);
    } catch (error: any) {
        showToast(error);
        return;
    }
}
</script>