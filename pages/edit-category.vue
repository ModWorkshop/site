<template>
    <flex gap="3" column class="content-block-large">
        <div class="content-block">
            <el-form @submit.native.prevent="save" style="display: flex; flex-direction: column;">
                <el-form-item label="Name" prop="name">
                    <el-input v-model="category.name"/>
                </el-form-item>
                <el-form-item label="Game">
                    <el-select v-model="category.game_id" placeholder="Select a game (Leave empty to create a game) " style="width: 100%;" clearable filterable>
                        <el-option v-for="game in games" :key="game.id" :label="game.name" :value="game.id"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Parent Category">
                    <el-select v-model="category.parent_id" placeholder="Select a parent category" style="width: 100%;" clearable filterable>
                        <el-option v-for="category in categories" :key="category.id" :label="category.path" :value="category.id"/>
                    </el-select>
                </el-form-item>
                <el-form-item class="mx-auto" style="width: unset">
                    <el-input type="submit" value="Save"/>
                </el-form-item>
            </el-form>
        </div>
    </flex>
</template>

<script setup>
    import { useStore } from "~~/store";
    import { ref, watch, useFetch, useContext } from '@nuxtjs/composition-api';

    const isNew = ref(true);
    const { $ftch, params } = useContext();
    const store = useStore();

    const category = ref({
        id: -1,
        name: '',
        game_id: null,
        parent_id: null
    });

    const categories = ref([]);
    const games = store.games;

    useFetch(async () => {
        await store.fetchGames();

        if (params.value.id) {
            isNew.value = false;
            category.value = await $ftch.get(`categories/${params.value.id}`);
        }
    });

    watch(() => category.value.game_id, async () => {
        if (category.value.game_id) {
            const cats = await $ftch.get(`/games/${category.game_id}/categories?include_paths=1`);
            categories.value = cats;
        } else {
            categories.value = [];
        }
    }, {immediate: true});

    const save = async function save() {
        try {
            if (isNew.value) {
                category.value = await $ftch.post('categories', category.value);
            } else {
                await $ftch.patch(`categories/${category.value.id}`, category.value);
            }
        } catch (error) {
            console.error(error);
            return;
        }
    };
</script>