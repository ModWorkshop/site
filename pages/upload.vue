<template>
    <page-block size="xs" :game="game" :breadcrumb="game ? breadcrumb : undefined">
        <Title>{{$t('upload_mod')}}</Title>
        <h1>{{$t('upload_mod')}}</h1>
        <a-form :model="mod" :created="false" @submit="create">
            <content-block v-if="step == 1">
                <h3 class="text-center">{{$t('mod_creation_1')}}</h3>
    
                <a-input v-model="mod.name" :label="$t('name')" maxlength="100" minlength="3" required :desc="$t('mod_name_desc')"/>
                <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>
                
                <a-game-select v-if="!gameName" v-model="mod.game_id" :label="$t('game')"/>

                <a-category-select 
                    v-if="categories?.data.length"
                    v-model="mod.category_id"
                    :max-height="200"
                    :label="$t('category')"
                    :categories="categories.data"
                />

                <flex class="mx-auto">
                    <a-button :disabled="disableCreate" @click="create(true)">{{$t('create_and_go')}}</a-button>
                    <a-button :disabled="disableCreate" @click="() => create(false)">{{$t('next')}}</a-button>
                </flex>
            </content-block>
            <content-block v-if="step == 2" gap="4">
                <h3 class="text-center">{{$t('mod_creation_2')}}</h3>
                <edit-mod-images v-if="mod.id" :mod="mod" light/>
                <flex class="mx-auto">
                    <a-button :to="`/mod/${mod.id}`">{{$t('go_to_mod_page')}}</a-button>
                    <a-button type="submit" @click="save(false, false)">{{$t('next')}}</a-button>
                </flex>
            </content-block>
            <content-block v-if="step == 3" gap="4">
                <h3 class="text-center">{{$t('mod_creation_3')}}</h3>
                <edit-mod-files v-if="mod.id" :mod="mod" light/>
                <flex class="mx-auto" column>
                    <a-input v-model="publish" :label="$t('publish_mod')" type="checkbox"/>
                    <a-button class="place-self-center" @click="save(true)">{{$t('finish')}}</a-button>
                </flex>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
import { FetchError } from 'ofetch';
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


const step = ref(1);
const publish = ref(true);

const showToast = useQuickErrorToast();

const mod: Ref<Mod> = ref({
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
    game_id: 0,
    version: '',
    user_id: store.user!.id,
    user: store.user!,
    downloads: 0,
    likes: 0,
    views: 0,
    visibility: 'public',
    suspended: false,
    comments_disabled: false,
    approved: false,
    has_download: false,
});

const gameName = computed(() => route.params.game);
const disableCreate = computed(() => (!route.params.game && !mod.value.game_id) || !mod.value.name || !mod.value.desc);

const { data: game } = await useResource<Game>('game', 'games');

if (game.value) {
    store.currentGame = game.value;
}

const breadcrumb = computed(() => {
    if (game.value) {
        return [
            { name: t('games'), to: 'games' },
            { name: game.value.name, to: `g/${gameName.value}` },
            { name: t('upload_mod') }
        ];
    }
});

const gameId = computed(() => game.value ? game.value.id : mod.value.game_id);

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories?include_paths=1`, {
    immediate: !!gameId.value
});

watch(gameId, val => {
    if (val) {
        refetchCats();
    }
    mod.value.category_id = undefined;
});

async function save(goToPage: boolean, publishMod?: boolean) {
    step.value = 3;

    try {
        await usePatch<Mod>(`mods/${mod.value.id}`, {...mod.value, publish: publishMod ?? publish.value});
        if (goToPage) {
            router.push(`/mod/${mod.value.id}`);
        } 
    } catch (error) {
        if (error instanceof FetchError) {
            showToast(error);
        }
        return;
    }
}

async function create(goToPage: boolean) {
    try {
        mod.value = await usePost<Mod>(`games/${gameId.value}/mods`, mod.value);
        if (goToPage) {
            router.push(`/mod/${mod.value.id}`);
        } else {
            step.value = 2;
        }
    } catch (error) {
        if (error instanceof FetchError) {
            showToast(error);
        }
        return;
    }
}
</script>