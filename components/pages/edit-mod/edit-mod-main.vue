<template>
    <a-input v-model="mod.name" label="Name" maxlength="150" minlength="3" required desc="Maximum of 150 letters and minimum of 3 letters"/>

    <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

    <flex v-if="categories" column gap="2">
        <a-input :label="$t('category')">
            <category-tree v-model="mod.category_id" style="max-height: 200px;" class="input-bg p-2 overflow-y-scroll" :categories="categories.data"/>
        </a-input>
    </flex>

    <a-select v-model="mod.tag_ids" :options="tags.data" multiple :label="$t('tags')" :desc="$t('make_your_mod_discoverable')"/>

    <a-select v-model="mod.visibility" :label="$t('visibility')" :options="visItems"/>

    <a-input v-if="!approvalOnlyForced" v-model="mod.send_for_approval" type="checkbox" :label="$t('send_for_approval')" :desc="$t('send_for_approval_desc')"/>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Category, Mod, Tag } from '~~/types/models.js';

const { t } = useI18n();

const props = defineProps<{
    mod: Mod
}>();

const visItems = [
    { name: t('public'), id: 'public' },
    { name: t('private'), id: 'private' },
    { name: t('unlisted'), id: 'unlisted' }
];

const gameId = computed(() => props.mod.game_id);
const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories`);
const { data: tags, refresh: refreshTags } = await useFetchMany<Tag>('tags', { 
    params: reactive({ 
        game_id: gameId,
        type: 'mod',
        global: 1
    })
});

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