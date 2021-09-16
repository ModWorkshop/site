<template>
    <flex class="form-group" column gap="2">
        <flex column class="label-area">
            <label v-if="label">{{label}}</label>
            <flex v-else-if="labels">
                <label v-for="lbl in labels" :key="lbl">{{lbl}}</label>
            </flex>
            <small v-if="desc">
                {{desc}}
            </small>
            <slot name="label-area"/>
        </flex>
        <flex>
            <slot/>
        </flex>
    </flex>
</template>

<script setup>
    defineProps({
        label: String,
        desc: String,
        labels: Array
    });
</script>

<style>
    .form-group .el-select, .form-group .el-input, .form-group label, .form-group .md-editor, .form-group .el-textarea {
        flex: 1;
    }

    .form-group .label-area {
        margin-left: 3px;
    }
</style>