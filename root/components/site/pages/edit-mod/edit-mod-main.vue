<template>
    <m-flex v-if="!mod.id" class="max-sm:flex-col" gap="2">
        <m-alert :title="$t('edit_mod_tips_title')">
            <ul style="padding-inline-start: 1rem;">
                <li>{{ $t('edit_mod_tip_1') }}</li>
                <li>{{ $t('edit_mod_tip_2') }}</li>
            </ul>
        </m-alert>
        <m-alert :title="$t('edit_mod_warns_title')" color="warning">
            <ul style="padding-inline-start: 1rem;">
                <li>{{ $t('edit_mod_warn_1') }}</li>
                <i18n-t keypath="edit_mod_warn_2" tag="li" scope="global">
                    <template #here>
                        <NuxtLink to="/document/rules">{{$t('here')}}</NuxtLink>
                    </template>
                </i18n-t>
            </ul>
        </m-alert>
    </m-flex>
    <m-input v-model="mod.name" placeholder="My Cool Mod" :label="$t('name')" maxlength="100" minlength="3" required :desc="$t('mod_name_desc')"/>

    <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

    <game-select v-if="!game && (isModerator || !mod.id)" v-model="mod.game_id" :label="$t('game')" required/>

    <category-select v-if="categories?.data.length" v-model="mod.category_id" :label="$t('category')" :desc="$t('category_desc')" :categories="categories.data"/>

    <m-select v-model="mod.tag_ids" :options="tags?.data" color-by="color" multiple list-tags :label="$t('tags')" :desc="$t('make_your_mod_discoverable')"/>

    <m-select v-model="mod.visibility" :label="$t('visibility')" :options="visItems"/>

    <m-input v-if="!approvalOnlyForced" v-model="mod.send_for_approval" type="checkbox" :label="$t('send_for_approval')" :desc="$t('send_for_approval_desc')"/>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { Category, Mod, Tag } from '~~/types/models.js';
import { useStore } from '~/store';

const { t } = useI18n();
const route = useRoute();

const { hasPermission } = useStore();
const mod = defineModel<Mod>({ required: true });
const isModerator = computed(() => hasPermission('manage-mods', mod.value.game));

const visItems = [
    { name: t('public'), id: 'public' },
    { name: t('private'), id: 'private' },
    { name: t('unlisted'), id: 'unlisted' }
];

const gameId = computed(() => mod.value.game_id);
const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories`, { immediate: !!gameId.value });
const { data: tags, refresh: refreshTags } = await useFetchMany<Tag>('tags', { 
    params: reactive({ 
        game_id: gameId,
        type: 'mod',
        global: 1
    }),
    immediate: !!gameId.value
});

const approvalOnlyForced = computed(() => {
    const category = categories.value?.data.find(cat => cat.id === mod.value.category_id);
    return mod.value.approved === null && (category?.approval_only ?? false);
});

const game = computed(() => route.params.game);

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