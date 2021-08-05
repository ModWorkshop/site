<template>
    <div class="content-block content-block-large">
        <el-form @submit.native.prevent="login" style="display: flex; flex-direction: column;">
            <el-tabs tab-position="left">
                <el-tab-pane label="Main">
                    <edit-mod-main :mod="mod"/>
                </el-tab-pane>
                <el-tab-pane label="Files & Images">

                </el-tab-pane>
                <el-tab-pane label="Extra">

                </el-tab-pane>
            </el-tabs>
        </el-form>
    </div>
</template>
<script>
export default {
    data() {
        return {
            mod: {
                name: '',
                description: '',
                root: 1,
                cid: 1,
                tags: [],
                version: '',
                nsfwMod: false,
                visibility: 1
            }
        }
    }
}
</script>
<style>
    
</style>