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
    import { computed, useContext, useFetch, watch } from "@nuxtjs/composition-api";

    let isNew = $ref(true);
    const { $factory, params, store, $axios } = useContext();
    let category = $ref({
        name: '',
        game_id: null,
        parent_id: null
    });

    let categories = $ref([]);
    let games = computed(() => store.getters.games);

    useFetch(async () => {
        await store.dispatch('fetchGames');

        if (params.value.id) {
            isNew = false;
            category = await $factory.getOne('categories', params.value.id);
        }
    });

    watch(() => category.game_id, async () => {
        if (category.game_id) {
            categories = await $axios.get(`/games/${category.game_id}/categories?include_paths=1`).then(res => res.data);
        } else {
            categories = [];
        }
    }, {immediate: true});

    const save = async function save() {
        try {
            if (isNew) {
                category = await $factory.create('categories', category);
            } else {
                await $factory.update('categories', category.id, category);
            }
        } catch (error) {
            console.error(error);
            return;
        }
    };
</script>