<template>
    <a-input v-model="mod.name" :label="$t('name')" maxlength="100" minlength="3" required :desc="$t('mod_name_desc')"/>

    <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

    <a-category-select v-if="categories?.data.length" v-model="mod.category_id" :label="$t('category')" :desc="$t('category_desc')" :categories="categories.data"/>

    <a-select v-model="mod.tag_ids" :options="tags?.data" color-by="color" multiple list-tags :label="$t('tags')" :desc="$t('make_your_mod_discoverable')"/>

    <a-select v-model="mod.visibility" :label="$t('visibility')" :options="visItems"/>

    <a-input v-if="!approvalOnlyForced" v-model="mod.send_for_approval" type="checkbox" :label="$t('send_for_approval')" :desc="$t('send_for_approval_desc')"/>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { Category, Mod, Tag } from '~~/types/models.js';

const { t } = useI18n();

const mod = defineModel<Mod>({ required: true });

const visItems = [
    { name: t('public'), id: 'public' },
    { name: t('private'), id: 'private' },
    { name: t('unlisted'), id: 'unlisted' }
];

const gameId = computed(() => mod.value.game_id);
const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories`);
const { data: tags, refresh: refreshTags } = await useFetchMany<Tag>('tags', { 
    params: reactive({ 
        game_id: gameId,
        type: 'mod',
        global: 1
    })
});

const approvalOnlyForced = computed(() => {
    const category = categories.value?.data.find(cat => cat.id === mod.value.category_id);
    return mod.value.approved === null && (category?.approval_only ?? false);
});

watch(() => categories.value, () => {
    if (categories.value && categories.value.data.length === 0) {
        mod.value.category_id = undefined;
    }
}); 

watch(gameId, val => {
    if (val) {
        refetchCats();
        refreshTags();
    }
});
</script>