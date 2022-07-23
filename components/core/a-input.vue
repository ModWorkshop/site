<template>
    <flex inline grow wrap :column="!isCheckbox" :gap="isCheckbox ? 1 : 2">
        <label v-if="!isCheckbox && label" :for="labelId">
            {{label}}
        </label>
        <textarea v-if="type == 'textarea'" v-model="modelValue" class="input" :maxlength="maxlength" :rows="rows" v-bind="$attrs" @input="$emit('update:modelValue', modelValue)"/>
        <va-color-input v-else-if="type == 'color'" v-model="modelValue" v-bind="$attrs" @input="$emit('update:modelValue', modelValue);"/>
        <slot v-else-if="$slots.default"/>
        <input v-else :id="labelId" v-bind="$attrs" ref="input" v-model="modelValue" class="input" :type="type" :maxlength="maxlength" @input="$emit('update:modelValue', modelValue);">
        <label v-if="isCheckbox && label" :for="labelId" class="basis-9/12">
            <span class="align-middle">{{label}}</span>
        </label>
        <small v-if="desc"><br v-if="isCheckbox">{{desc}}</small>
    </flex>
</template>

<script setup lang="ts">
    const props = defineProps({
        id: String,
        desc: String,
        label: String,
        modelValue: [String, Boolean],
        maxlength: [Number, String],
        rows: [Number, String],
        type: String,
        value: String,
    });

    const input = ref<HTMLElement>(null);

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