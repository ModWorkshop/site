<template>
    <flex class="input-container" inline wrap :column="!isCheckbox" :gap="isCheckbox ? 1 : 2">
        <label v-if="!isCheckbox && label" :for="labelId">
            {{label}}
        </label>
        <flex v-if="!$slots.default">
            <input 
                v-if="type == 'color'"
                v-bind="$attrs"
                v-model="modelValue"
                :class="classes"
                style="flex-grow: 1;"
                @input="$emit('update:modelValue', modelValue);"
            >
            <textarea 
                v-if="type == 'textarea'"
                v-model="(modelValue as string)"
                class="input"
                :rows="rows"
                v-bind="$attrs"
                @input="$emit('update:modelValue', modelValue)"
            />
            <input 
                v-else-if="isCheckbox" 
                :id="labelId"
                v-bind="$attrs"
                ref="input"
                v-model="(modelValue as boolean)"
                :class="classes"
                type="checkbox"
                @change="$emit('update:modelValue', modelValue);"
            >
            <input 
                v-else
                :id="labelId"
                v-bind="$attrs"
                ref="input"
                v-model="modelValue"
                :class="classes"
                :type="type"
                @input="$emit('update:modelValue', modelValue);"
            >
        </flex>
        <slot v-else/>
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
    modelValue: [String, Number, Boolean],
    rows: [Number, String],
    type: String,
    value: String,
});

const vm = toRef(props, 'modelValue');
const input = ref<HTMLInputElement>(null);
const err = useWatchValidation(vm, input);

const uniqueId = useGetUniqueId();
const labelId = computed(() => props.id || uniqueId);

defineEmits(['update:modelValue']);

const isCheckbox = computed(() => props.type == 'checkbox');

const classes = computed(() => ({input: true, 'input-error': !!err.value}));
</script>

<style>
.flex > .input-container:not(.flex-col > .input-container) {
    flex: 1;
}
</style>

<style scoped>
.input {
    padding: 0.6rem;
    flex: 1;
    height: auto;
    color: var(--text-color);
    background-color: var(--input-bg-color);
    border: 1px solid var(--input-border-color);
    border-radius: var(--border-radius);
    resize: vertical;
}

.input:focus-visible {
    outline: none;
    border-color: var(--primary-color)
}

.input[type='color'] {
    flex: 6;
}

.input[type='checkbox'] {
    width: revert;
    margin-top: 3px;
}

.input:disabled {
    opacity: 0.4;
}
</style>