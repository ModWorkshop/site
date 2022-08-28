<template>
    <flex grow inline wrap :column="!isCheckbox" :gap="isCheckbox ? 1 : 2">
        <label v-if="!isCheckbox && label" :for="labelId">
            {{label}}
        </label>
        <textarea v-if="type == 'textarea'" v-model="(modelValue as string)" class="input" :rows="rows" v-bind="$attrs" @input="$emit('update:modelValue', modelValue)"/>
        <va-color-input v-else-if="type == 'color'" v-model="(modelValue as string)" v-bind="$attrs" @input="$emit('update:modelValue', modelValue);"/>
        <slot v-else-if="$slots.default"/>
        <input v-else-if="isCheckbox" :id="labelId" v-bind="$attrs" ref="input" v-model="(modelValue as boolean)" :class="{input: true, 'input-error': !!err}" type="checkbox" @change="$emit('update:modelValue', modelValue);">
        <input v-else :id="labelId" v-bind="$attrs" ref="input" v-model="modelValue" :class="{input: true, 'input-error': !!err}" :type="type" @input="$emit('update:modelValue', modelValue);">
        <label v-if="isCheckbox && label" :for="labelId" class="flex-grow">
            <span class="align-middle">{{label}}</span>
        </label>
        <span v-if="err" class="text-danger">{{err}}</span>
        <hr v-if="isCheckbox && desc">
        <small v-if="desc">{{desc}}</small>
    </flex>
</template>

<script setup lang="ts">
    const props = defineProps({
        id: String,
        desc: String,
        label: String,
        modelValue: [String, Boolean],
        rows: [Number, String],
        type: String,
        value: String,
    });

    const vm = toRef(props, 'modelValue');
    const input = ref<HTMLInputElement>(null);
    const err = useWatchValidation(vm, input);

    const uniqueId = useGetUniqueId();
    const labelId = computed(() => props.id || uniqueId);

    defineEmits([
        'update:modelValue', 
    ]);

    const isCheckbox = computed(() => props.type == 'checkbox');

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