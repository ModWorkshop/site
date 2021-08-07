<template>
    <div>
        <el-form-item label="Name" prop="name">
            <el-input v-model="mod.name"/>
        </el-form-item>
        <el-form-item label="Description" prop="description">
            <el-input type="textarea" v-model="mod.desc" rows="8"/> <!-- TODO: Markdown editor -->
        </el-form-item>
    </div>
</template>
<script>
export default {
    props: {
        mod: Object,
    },
}
</script>
<style>
    
</style>