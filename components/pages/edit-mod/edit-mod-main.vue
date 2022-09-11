<template>
    <a-input v-model="mod.name" label="Name" maxlength="150" minlength="3" required desc="Maximum of 150 letters and minimum of 3 letters"/>

    <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

    <a-input v-model="mod.short_desc" label="Short Description" type="textarea" rows="2" maxlength="150" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods"/>

    <a-select v-model="mod.game_id" label="Game" placeholder="Select a game" :options="store.games?.data"/>
    <flex v-if="categories" column gap="2">
        <label>Category</label>
        <category-tree v-model="mod.category_id" style="height: 200px;" class="input p-2 overflow-y-scroll" :categories="categories.data"/>
    </flex>

    <a-select v-model="mod.tag_ids" placeholder="Select tags" :options="tags.data" multiple label="Tags" desc="Make your mod more discoverable"/>

    <a-select v-model="mod.visibility" label="Visiblity" placeholder="Select a category" :options="visItems"/>

    <a-input v-model="mod.comments_disabled" type="checkbox" :label="$t('disable_comments')"/>

    <details>
        <summary>{{$t('license')}}</summary>
        <flex class="mt-3" column gap="2">
            <small><a href="https://choosealicense.com/" target="_blank">Can't choose?</a></small>
            <md-editor v-model="mod.license" rows="6"/>
        </flex>
    </details>

    <a-alert class="w-full" color="danger" :title="$t('danger_zone')">
        <div>
            <a-button color="danger" @click="deleteMod">{{$t('delete')}}</a-button>
        </div>
    </a-alert>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Category, Mod, Tag } from '~~/types/models.js';
const { init: openModal } = useModal();

const router = useRouter();

const props = defineProps<{
    mod: Mod
}>();

const visItems = [
    { name: 'Public', value: 1 },
    { name: 'Hidden', value: 2 },
    { name: 'Unlisted', value: 3 }
];

function deleteMod() {
    openModal({
        message: 'Are you sure you want to delete the mod?',
        async onOk() {
            await useDelete(`mods/${props.mod.id}`);
            router.push('/');
        }
    });
}

const gameId = computed(() => props.mod.game_id);
const store = useStore();
const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories`);
const { data: tags, refresh: refreshTags } = await useFetchMany<Tag>('tags', { 
    params: reactive({ 
        game_id: gameId,
        type: 'mod',
        global: 1
    })
});
await store.fetchGames();

watch(gameId, val => {
    if (val) {
        refetchCats();
        refreshTags();
    }
});
</script>