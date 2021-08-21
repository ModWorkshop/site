<template>
    <div class="content-block content-block-large">
        <el-form @submit.native.prevent="save" style="display: flex; flex-direction: column;">
            <el-tabs tab-position="left">
                <el-tab-pane label="Main">
                    <edit-mod-main :mod="mod"/>
                </el-tab-pane>
                <el-tab-pane label="Files & Images">

                </el-tab-pane>
                <el-tab-pane label="Extra">

                </el-tab-pane>
            </el-tabs>
            <el-form-item class="mx-auto" style="width: unset">
                <el-input type="submit" value="Save"/>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
export default {
    data() {
        return {
            mod: {
                name: '',
                desc: '',
                root: 1,
                cid: 1,
                tags: [],
                version: '',
                nsfwMod: false,
                visibility: 1
            }
        }
    },
    methods: {
        async save() {
            try {
                await this.$axios.post('/mods', this.mod);
            } catch (error) {
                console.error(error);
                return;
            }
        },
    },
    async asyncData({params, $factory}) {
        if (params.id) {
            return { mod: await $factory.getOne('mods', params.id) };
        }
    }
}
</script>
<style>
    
</style>