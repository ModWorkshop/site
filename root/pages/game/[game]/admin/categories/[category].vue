<template>
    <simple-resource-form v-if="category" v-model="category" url="categories" :game="game" :redirect-to="categoriesPage">
        <a-input v-model="category.name" :label="$t('name')"/>
        <a-input v-model="category.webhook_url" :label="$t('webhook_url')" :desc="$t('webhook_url_desc')"/>
        <a-input v-model="category.approval_only" :label="$t('approval_only')" type="checkbox" :desc="$t('approval_only_desc')"/>
        <md-editor v-model="category.desc" :label="$t('description')"/>
        <flex v-if="categories" column gap="2">
            <label>{{$t("parent_category")}}</label>
            <category-tree v-model="category.parent_id" style="height: 200px;" class="input p-2 overflow-y-scroll" :categories="validCategories"/>
        </flex>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { Category, Game } from "~~/types/models";

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-categories', props.game);

const route = useRoute();
const router = useRouter();
const { t } = useI18n();
const yesNoModal = useYesNoModal();

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

function deleteCategory() {
    yesNoModal({
        desc: t('delete_category_warning'),
        async yes() {
            await useDelete(`categories/${category.value.id}`);
            router.push('/');
        }
    });
}
</script>