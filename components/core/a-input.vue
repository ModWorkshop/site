<template>
    <flex inline grow wrap :column="!isCheckbox" :gap="isCheckbox ? 1 : 2">
        <label v-if="!isCheckbox && label" :for="input?.id">
            {{label}}
        </label>
        <textarea v-if="type == 'textarea'" v-model="modelValue" class="input" :maxlength="maxlength" :rows="rows" v-bind="$attrs" @input="$emit('update:modelValue', modelValue)"/>
        <va-color-input v-else-if="type == 'color'" v-model="modelValue" v-bind="$attrs" @input="$emit('update:modelValue', modelValue);"/>
        <slot v-else-if="$slots.default"/>
        <input v-else v-bind="$attrs" ref="input" v-model="modelValue" v-uid class="input" :type="type" :maxlength="maxlength" @input="$emit('update:modelValue', modelValue);">
        <label v-if="isCheckbox && label" :for="input?.id" class="basis-11/12">
            <span class="align-middle">{{label}}</span>
        </label>
        <small v-if="desc"><br v-if="isCheckbox">{{desc}}</small>
    </flex>
</template>

<script setup>
    const props = defineProps({
        desc: String,
        label: String,
        modelValue: [String, Boolean],
        maxlength: [Number, String],
        rows: [Number, String],
        type: String,
        value: String,
    });

    defineEmits([
        'update:modelValue', 
    ]);

    const isCheckbox = computed(() => props.type == 'checkbox');

    const input = ref(null);
</script>

<style scoped>
    .input {
        padding: 0.6rem;
        width: 100%;
        color: var(--text-color);
        background-color: var(--input-bg-color);
        border: 1px solid var(--input-border-color);
        border-radius: var(--border-radius);
        resize: vertical;
    }

    .input[type='checkbox'] {
        width: revert;
        margin-top: 3px;
    }
</style>