<template>
    <div class="p-4">
        <el-form-item label="Name" prop="name">
            <el-input v-model="mod.name"/>
        </el-form-item>
        <form-item label="Description" label-id="desc">
            <md-editor v-model="mod.desc" rows="16"/>
        </form-item>
        <el-form-item label="Game" prop="game">
            <el-select v-model="mod.game_id" placeholder="Select a game" style="width: 100%;">
                <el-option v-for="game in games" :key="game.name" :label="game.name" :value="game.id"/>
            </el-select>
        </el-form-item>
        <el-form-item label="Category" prop="category">
            <el-select v-model="mod.category_id" placeholder="Select a category" style="width: 100%;" clearable>
                
            </el-select>
        </el-form-item>
        <el-form-item label="Tags" prop="tags">
            <el-select v-model="mod.tags" placeholder="Select tags" style="width: 100%;" clearable multiple>
                <el-option label="Test" value="1"/>
                <el-option label="This" value="2"/>
                <el-option label="Doesn't" value="3"/>
                <el-option label="Work" value="4"/>
                <el-option label="Yet" value="5"/>
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
import { mapGetters } from 'vuex';
export default {
    props: {
        mod: Object,
    },
    computed: {
        games() {
            const cats = this.$store.getters.categories;
            return cats;
        }
    },
    async fetch() {
        await this.$store.dispatch('fetchCategories');
    }
}
</script>