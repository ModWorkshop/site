<template>
    <page-block>
        <div class="content-block">
            <a-form @submit="save" style="display: flex; flex-direction: column;">
                <a-input label="Name" v-model="category.name"/>
                <a-select label="Game" v-model="category.game_id" placeholder="Select a game" clearable :options="games"/>
                <a-select label="Parent Category" v-model="category.parent_id" placeholder="Select a parent category" style="width: 100%;" clearable :options="categories"/>
            </a-form>
        </div>
    </page-block>
</template>

<script setup>
    import { useStore } from "~~/store";

    const store = useStore();

    const category = ref({
        id: -1,
        name: '',
        game_id: null,
        parent_id: null
    });

    await store.fetchGames();

    const categories = ref([]);
    const games = store.games;
    const route = useRoute();


    if (params.value.id) {
        category.value = await useAPIFetch(`categories/${route.params.id}`);
    }

    watch(() => category.value.game_id, async () => {
        if (category.value.game_id) {
            const cats = await useGet(`/games/${category.game_id}/categories?include_paths=1`);
            categories.value = cats;
        } else {
            categories.value = [];
        }
    }, {immediate: true});

    async function save() {
        try {
            if (category.value.id == -1) {
                category.value = await usePatch(`categories/${category.value.id}`, category.value);
            } else {
                category.value = await usePost('categories', category.value);
            }
        } catch (error) {
            console.error(error);
            return;
        }
    };
</script>