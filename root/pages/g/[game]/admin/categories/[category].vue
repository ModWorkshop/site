<template>
    <simple-resource-form v-if="category" v-model="category" url="categories" :game="game" :redirect-to="categoriesPage">
        <a-input v-model="category.name" :label="$t('name')"/>
        <a-input v-model="category.display_order" :label="$t('order')" type="number"/>
        <a-input v-model="category.webhook_url" :label="$t('webhook_url')" :desc="$t('webhook_url_desc')"/>
        <a-input v-model="category.approval_only" :label="$t('approval_only')" type="checkbox" :desc="$t('approval_only_desc')"/>
        <md-editor v-model="category.desc" :label="$t('description')"/>
        <template #danger-zone>
            <a-button v-if="hasPermission('admin')" color="danger" @click="showMoveMods = true"><i-mdi:cursor-move/> Move Mods</a-button>
        </template>
        <flex v-if="categories" column gap="2">
            <label>{{$t("parent_category")}}</label>
            <a-category-select v-model="category.parent_id" class="input p-2 overflow-y-scroll" :categories="validCategories"/>
        </flex>
        <a-modal-form v-model="showMoveMods" title="Move Mods" @submit="moveMods">
            <a-alert color="danger">
                Moving mods is a dangerous procedure! Do it only if you are 100% sure.
            </a-alert>
            <a-category-select v-model="moveModsCategoryId" class="input p-2 overflow-y-scroll" :categories="validMoveCategories"/>
            <a-input v-model="areYouSure" type="checkbox" :label="$t('are_you_sure')"/>
        </a-modal-form>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Category, Game } from "~~/types/models";
import { useStore } from '~~/store';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-categories', props.game);

const route = useRoute();

const { hasPermission } = useStore();
const showError = useQuickErrorToast();
const moveModsCategoryId = ref();
const areYouSure = ref(false);
const showMoveMods = ref(false);
watch(() => showMoveMods, () => areYouSure.value = false);

const gameId = route.params.game;
const categoriesPage = getAdminUrl('categories', props.game);

const { data: category } = await useEditResource<Category>('category', 'categories', {
    name: '',
    id: 0,
    game_id: parseInt(gameId as string),
    parent_id: null,
    short_name: "",
    desc: "",
    hidden: false,
    grid: false,
    disporder: 0,
    thumbnail: "",
    banner: "",
    buttons: "",
    webhook_url: "",
    approval_only: false,
    last_date: "",
    created_at: "",
    updated_at: ""
});

const { data: categories } = await useFetchMany<Category>(`games/${gameId}/categories`);

const validCategories = computed(() => categories.value?.data.filter(cat => {
    //Don't include the category itself
    if (cat.id === category.value!.id) {
        return false;
    }

    if (!cat.parent_id) {
        return true;
    }

    //Don't include any child categories to avoid circular reference
    return cat.parent_id != category.value.id;
}) || []);


const validMoveCategories = computed(() => categories.value?.data.filter(cat => cat.id !== category.value!.id) || []);

async function moveMods() {
    try {
        await patchRequest(`categories/${category.value.id}/mods`, {
            category_id: moveModsCategoryId.value,
            are_you_sure: areYouSure.value
        });

        showMoveMods.value = false;
    } catch (e) {
        showError(e);
    } 
}
</script>