<template>
    <div class="p-4">
        <el-form-item label="Name" prop="name">
            <el-input v-model="mod.name"/>
        </el-form-item>
        <form-item label="Description" label-id="desc">
            <md-editor v-model="mod.desc" rows="16"/>
        </form-item>
        <el-form-item label="Game" prop="game">
            <el-select v-model="mod.game_id" placeholder="Select a game" style="width: 100%;" filterable>
                <el-option v-for="game in games" :key="game.id" :label="game.name" :value="game.id"/>
            </el-select>
        </el-form-item>
        <el-form-item label="Category" prop="category">
            <el-select v-model="mod.category_id" placeholder="Select a category" style="width: 100%;" clearable filterable>
                <el-option v-for="category in categories" :key="category.id" :label="category.path" :value="category.id"/>
            </el-select>
        </el-form-item>
        <el-form-item label="Tags" prop="tags">
            <el-select v-model="mod.tag_ids" placeholder="Select tags" style="width: 100%;" clearable multiple filterable>
                <el-option v-for="tag in tags" :key="tag.id" :label="tag.name" :value="tag.id"/>
            </el-select>
        </el-form-item>
        <el-form-item label="Version" prop="version">
            <el-input v-model="mod.version"/>
        </el-form-item>
        <el-form-item prop="is_nsfw">
            <el-checkbox v-model="mod.is_nsfw">NSFW Mod</el-checkbox>
            <br>
            <small>If the mod contains adult content you must set your mod as NSFW. Read the rules regarding NSFW or risk getting banned.</small>
        </el-form-item>
        <el-form-item label="Visibility" prop="visibility">
            <el-select v-model="mod.visibility" placeholder="Select a category" style="width: 100%;">
                <el-option label="Public" value="1"/>
                <el-option label="Hidden" value="2"/>
                <el-option label="Unlisted" value="3"/>
                <el-option label="Invite Only" value="4"/>
            </el-select>
        </el-form-item>
    </div>
</template>
<script>
import { computed, ref, useContext, useFetch, useStore, watch } from '@nuxtjs/composition-api';

export default {
    props: {
        mod: Object
    },
    setup({ mod }) {
        const { $axios } = useContext();
        const $store = useStore();

        const categories = ref([]);
        const tags = ref([]);
        const games = computed(() => $store.getters.games);
        useFetch(async () => {
            await $store.dispatch('fetchGames');
        
            tags.value = await $axios.get('/tags').then(res => res.data);
        });

        watch(() => mod.game_id, async () => {
            if (!mod.game_id) {
                return;
            }
            categories.value = await $axios.get(`/games/${mod.game_id}/categories?include_paths=1`).then(res => res.data);
        }, {immediate: true});

        return { tags, categories, games };
    },
}
</script>