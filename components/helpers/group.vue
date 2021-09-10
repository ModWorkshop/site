<template>
    <flex class="form-group" column gap="4">
        <label v-if="label">{{label}}</label>
        <flex v-else-if="labels">
            <label v-for="lbl in labels" :key="lbl">{{lbl}}</label>
        </flex>
        <flex>
            <slot/>
        </flex>
    </flex>
</template>

<script setup>
    defineProps({
        label: String,
        labels: Array
    });
</script>

<style>
    .form-group .el-select, .form-group .el-input, .form-group label, .form-group .md-editor {
        flex: 1;
    }

    .form-group label {
        margin-left: 3px;
    }
</style>