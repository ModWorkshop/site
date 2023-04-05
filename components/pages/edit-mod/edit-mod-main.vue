<template>
    <a-alert v-if="!mod.published_at && hasDownload" color="warning">
        {{$t('publish_mod_desc')}}
        <a-button class="mr-auto" icon="mdi:upload" @click="publish">{{ $t('publish_mod') }}</a-button>
    </a-alert>

    <a-input v-model="mod.name" :label="$t('name')" maxlength="150" minlength="3" required :desc="$t('mod_name_desc')"/>

    <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

    <flex v-if="categories" column gap="2">
        <a-input :label="$t('category')">
            <category-tree v-model="mod.category_id" style="max-height: 200px;" class="input-bg p-2 overflow-y-scroll" :categories="categories.data"/>
        </a-input>
    </flex>

    <a-select v-model="mod.tag_ids" :options="tags?.data" color-by="color" multiple list-tags :label="$t('tags')" :desc="$t('make_your_mod_discoverable')"/>

    <a-select v-model="mod.visibility" :label="$t('visibility')" :options="visItems"/>

    <a-input v-if="!approvalOnlyForced" v-model="mod.send_for_approval" type="checkbox" :label="$t('send_for_approval')" :desc="$t('send_for_approval_desc')"/>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Category, Mod, Tag } from '~~/types/models.js';
import { FetchError } from 'ofetch';

const { t } = useI18n();
const showToast = useQuickErrorToast();

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

const hasDownload = computed(() => {
    const files = props.mod.files;
    const links = props.mod.links;

    return (files && files?.data.length > 0) || (links && links?.data.length > 0);
});

const approvalOnlyForced = computed(() => {
    const category = categories.value?.data.find(cat => cat.id === props.mod.category_id);
    return props.mod.approved === null && (category?.approval_only ?? false);
});

async function publish() {
    try {
        const mod = await usePatch<Mod>(`mods/${props.mod.id}`, { publish: true });
        props.mod.published_at = mod.published_at;
    } catch (error) {
        if (error instanceof FetchError) {
            showToast(error);
        }
        return;
    }
}

watch(gameId, val => {
    if (val) {
        refetchCats();
        refreshTags();
    }
});
</script>