<template>
    <flex class="form-group" column grow gap="2">
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
        <flex :column="column" :gap="gap">
            <slot/>
        </flex>
        <small v-if="error">
            {{error}}
        </small>
    </flex>
</template>

<script setup>
import { ref, watch, inject } from '@nuxtjs/composition-api';

const props = defineProps({
    column: Boolean,
    gap: [Number, String],
    check: String,
    label: String,
    desc: String,
    labels: Array
});

const rules = inject('rules', false);
const model = inject('model', false);
const error = ref('');

if (rules && model && props.check) {
    watch(() => model[props.check], val => {
        error.value = '';
        const rule = rules[props.check];
        if (rule.min) {
            if (val.length < rule.min) {
                error.value = `Must be at least ${rule.min} characters long`;
            }
        }
        if (rule.max) {
            if (val.length > rule.max) {
                error.value = `Must not exceed ${rule.max} characters`;
            }
        }
    });
}
</script>

<style>
    .form-group .el-select, .form-group .el-input, .form-group label, .form-group .md-editor, .form-group .el-textarea {
        flex: 1;
    }

    .form-group .label-area {
        margin-left: 3px;
    }
</style>