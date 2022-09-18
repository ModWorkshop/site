<template>
    <a-input v-model="mod.name" label="Name" maxlength="150" minlength="3" required desc="Maximum of 150 letters and minimum of 3 letters"/>

    <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

    <a-select v-model="mod.game_id" label="Game" placeholder="Select a game" :options="store.games?.data"/>
    <flex v-if="categories" column gap="2">
        <label>Category</label>
        <category-tree v-model="mod.category_id" style="height: 200px;" class="input p-2 overflow-y-scroll" :categories="categories.data"/>
    </flex>

    <a-select v-model="mod.tag_ids" placeholder="Select tags" :options="tags.data" multiple label="Tags" desc="Make your mod more discoverable"/>

    <a-select v-model="mod.visibility" label="Visiblity" placeholder="Select a category" :options="visItems"/>

    <a-input v-if="!approvalOnlyForced" v-model="mod.send_for_approval" type="checkbox" :label="$t('send_for_approval')" :desc="$t('send_for_approval_desc')"/>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Category, Mod, Tag } from '~~/types/models.js';


const props = defineProps<{
    mod: Mod
}>();

const visItems = [
    { name: 'Public', id: 1 },
    { name: 'Hidden', id: 2 },
    { name: 'Unlisted', id: 3 }
];

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

const approvalOnlyForced = computed(() => {
    const category = categories.value.data.find(cat => cat.id === props.mod.category_id);
    return props.mod.approved === null && (category?.approval_only ?? false);
});

watch(gameId, val => {
    if (val) {
        refetchCats();
        refreshTags();
    }
});
</script>