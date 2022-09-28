<template>
    <page-block size="sm">
        <a-form :model="mod" :created="false" @submit="create">
            <content-block class="p-8">
                <h1>
                    Upload Mod
                </h1>
                <h4 class="whitespace-pre">{{$t('mod_creation_desc')}}</h4>
    
                <a-input v-model="mod.name" label="Name" maxlength="150" minlength="3" required desc="Maximum of 150 letters and minimum of 3 letters"/>
                <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

                <a-select v-model="mod.game_id" label="Game" placeholder="Select a game" :options="store.games?.data"/>
                <flex v-if="categories" column gap="2">
                    <label>Category</label>
                    <category-tree v-model="mod.category_id" style="height: 200px;" class="input p-2 overflow-y-scroll" :categories="categories.data"/>
                </flex>

                <flex class="mx-auto">
                    <a-button type="submit" :disabled="!mod.name || !mod.desc">Create</a-button>
                </flex>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
import { Ref } from 'vue';
import { useStore } from '~~/store';
import { Category, Mod } from '~~/types/models';

definePageMeta({
    middleware: 'unbanned-users-only'
});

const { user } = useStore();
const router = useRouter();
const store = useStore();
await store.fetchGames();

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
    user_id: user.id,
    user,
    downloads: 0,
    likes: 0,
    views: 0,
    visibility: 1,
    suspended: false,
    comments_disabled: false,
    approved: false,
    has_download: false,
});

const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${mod.value.game_id}/categories?include_paths=1`, {
    immediate: !!mod.value.game_id
});

watch(() => mod.value.game_id, val => {
    if (val) {
        refetchCats();
    }
});

async function create() {
    try {
        const fetchedMod = await usePost<Mod>(`games/${mod.value.game_id}/mods`, mod.value);
        router.push(`mod/${fetchedMod.id}/edit`);
    } catch (error) {
        console.error(error);
        return;
    }
}
</script>