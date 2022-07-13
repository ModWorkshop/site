<template>
    <flex inline grow :column="!isCheckbox" gap="2">
        <label v-if="!isCheckbox && label" :for="input?.id">
            {{label}}
        </label>
        <textarea v-if="type == 'textarea'" class="input" :maxlength="maxlength" :rows="rows" @input="$emit('update:modelValue', modelValue)" v-bind="$attrs">{{modelValue}}</textarea>
        <va-color-input v-else-if="type == 'color'" v-model="modelValue" @input="$emit('update:modelValue',  modelValue);" v-bind="$attrs"/>
        <slot v-else-if="$slots.default"/>
        <input v-else v-model="modelValue" @input="$emit('update:modelValue',  modelValue);" class="input" :type="type" :maxlength="maxlength" v-bind="$attrs" v-uid ref="input"/>
        <label v-if="isCheckbox && label" :for="input?.id">
            {{label}}
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

    const emit = defineEmits([
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
    }
</style>