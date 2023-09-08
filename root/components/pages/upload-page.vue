<template>
    <div style="width: 80%; align-self: center;">
        <Title>{{$t('upload_mod')}}</Title>
        <a-form v-model="mod" :created="false" @submit="create">
            <content-block v-if="step == 1" padding="8">
                <h2 class="text-center">{{$t('mod_creation_1')}}</h2>
    
                <a-input v-model="mod.name" :label="$t('name')" maxlength="100" minlength="3" required :desc="$t('mod_name_desc')"/>
                <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>
                
                <a-game-select v-if="!gameName" v-model="mod.game_id" :label="$t('game')"/>

                <a-category-select 
                    v-if="categories?.data.length"
                    v-model="mod.category_id"
                    :max-height="350"
                    :label="$t('category')"
                    :categories="categories.data"
                />

                <a-select v-model="mod.visibility" :label="$t('visibility')" :options="visItems"/>

                <flex class="mx-auto">
                    <a-button :disabled="disableCreate" @click="create(true)">{{$t('create_and_go')}}</a-button>
                    <a-button :disabled="disableCreate" @click="() => create(false)">{{$t('next')}}</a-button>
                </flex>
            </content-block>
            <content-block v-if="step == 2" gap="4">
                <h3 class="text-center">{{$t('mod_creation_2')}}</h3>
                <edit-mod-images v-if="mod.id" v-model="mod" light/>
                <flex class="mx-auto">
                    <a-button :to="`/mod/${mod.id}`">{{$t('go_to_mod_page')}}</a-button>
                    <a-button type="submit" @click="save(false, false)">{{$t('next')}}</a-button>
                </flex>
            </content-block>
            <content-block v-if="step == 3" gap="4">
                <h3 class="text-center">{{$t('mod_creation_3')}}</h3>
                <edit-mod-files v-if="mod.id" v-model="mod" light/>
                <flex class="mx-auto" column>
                    <a-input v-if="mod.visibility == 'public'" v-model="publish" :label="$t('publish_mod')" type="checkbox"/>
                    <a-button class="place-self-center" @click="save(true)">{{$t('finish')}}</a-button>
                </flex>
            </content-block>
        </a-form>
    </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Category, Game, Mod } from '~~/types/models';

const store = useStore();
const router = useRouter();
const route = useRoute();
const { t } = useI18n();


const step = ref(1);
const publish = ref(true);

const showToast = useQuickErrorToast();

const { game } = defineProps<{
    game?: Game
}>();

if (game) {
    store.currentGame = game;
}

const visItems = [
    { name: t('public'), id: 'public' },
    { name: t('private'), id: 'private' },
    { name: t('unlisted'), id: 'unlisted' }
];

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

const gameId = computed(() => game ? game.id : mod.value.game_id);

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories?include_paths=1`, {
    immediate: !!gameId.value,
    lazy: true
});

watch(gameId, val => {
    if (val) {
        refetchCats();
    } else {
        categories.value = null;
    }
    mod.value.category_id = undefined;
}, { immediate: true });

async function save(goToPage: boolean, publishMod?: boolean) {
    step.value = 3;

    try {
        await patchRequest<Mod>(`mods/${mod.value.id}`, {...mod.value, publish: publishMod ?? (publish.value && mod.value.visibility == 'public')});
        if (goToPage) {
            router.push(`/mod/${mod.value.id}`);
        } 
    } catch (error) {
        showToast(error);
    }
}

async function create(goToPage: boolean) {
    try {
        mod.value.game_id = gameId.value;
        mod.value = await postRequest<Mod>(`games/${gameId.value}/mods`, mod.value);
        if (goToPage) {
            router.push(`/mod/${mod.value.id}`);
        } else {
            step.value = 2;
        }
    } catch (error) {
        showToast(error);
    }
}
</script>