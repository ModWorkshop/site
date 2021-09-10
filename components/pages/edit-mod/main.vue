<template>
    <flex column gap="4">
        <group label="Name">
            <el-input v-model="mod.name"/>
        </group>
        <group label="Description" label-id="desc">
            <md-editor v-model="mod.desc" rows="12"/>
        </group>
        <group label="Short Description">
            <el-input type="textarea" v-model="mod.short_description" rows="2"/>
        </group>
        <group :labels="['Game', 'Category']">
            <el-select v-model="mod.game_id" placeholder="Select a game" filterable>
                <el-option v-for="game in games" :key="game.id" :label="game.name" :value="game.id"/>
            </el-select>
            <el-select v-model="mod.category_id" placeholder="Select a category" clearable filterable>
                <el-option v-for="category in categories" :key="category.id" :label="category.path" :value="category.id"/>
            </el-select>
        </group>
        <group label="Tags">
            <el-select v-model="mod.tag_ids" placeholder="Select tags" clearable multiple filterable>
                <el-option v-for="tag in tags" :key="tag.id" :label="tag.name" :value="tag.id"/>
            </el-select>
        </group>
        <group label="Visiblity">
            <el-select v-model="mod.visibility" placeholder="Select a category">
                <el-option label="Public" :value="1"/>
                <el-option label="Hidden" :value="2"/>
                <el-option label="Unlisted" :value="3"/>
                <el-option label="Invite Only" :value="4"/>
            </el-select>
        </group>
        <group label="Donation">
            <el-input v-model="mod.donation"/>
        </group>
        <group label="License">
            <md-editor v-model="mod.license" rows="6"/>
        </group>

        <group>
            <div>
                <el-checkbox v-model="mod.is_nsfw">NSFW Mod</el-checkbox>
                <small>(If the mod contains adult content you must set your mod as NSFW. Read the rules regarding NSFW or risk getting banned)</small>
            </div>
        </group>
    </flex>
</template>

<script setup>
    import { computed, useContext, useFetch, useStore, watch } from '@nuxtjs/composition-api';

    const props = defineProps({
        modData: Object
    });

    const { $axios } = useContext();
    const $store = useStore();
    const mod = computed(() => props.modData);

    let categories = $ref([]);
    let tags = $ref([]);
    const games = computed(() => $store.getters.games);
    useFetch(async () => {
        await $store.dispatch('fetchGames');
        tags = await $axios.get('/tags').then(res => res.data);
    });

    watch(() => mod.value.game_id, async () => {
        if (mod.value.game_id) {
            try {
                categories = await $axios.get(`/games/${mod.value.game_id}/categories?include_paths=1`).then(res => res.data);
            } catch (e) {
                console.log(e);
            }
        } else {
            categories = [];
        }
    }, {immediate: true});
</script>