<template>
    <flex class="input-container" inline wrap :column="!isCheckbox" :gap="isCheckbox ? 1 : 2">
        <label v-if="!isCheckbox && label" :for="labelId">
            {{label}}
        </label>
        <flex v-if="!$slots.default">
            <input 
                v-if="type == 'color'"
                ref="input"
                v-bind="$attrs"
                v-model="vm"
                :class="classes"
                style="flex-grow: 1;"
                @input="$emit('update:modelValue', vm);"
            >
            <textarea 
                v-if="type == 'textarea'"
                ref="input"
                v-model="vm"
                class="mw-input"
                :rows="rows"
                v-bind="$attrs"
                @input="$emit('update:modelValue', vm)"
            />
            <input 
                v-else-if="isCheckbox" 
                :id="labelId"
                v-bind="$attrs"
                ref="input"
                v-model="vm"
                :class="classes"
                type="checkbox"
                @change="$emit('update:modelValue', vm);"
            >
            <input 
                v-else
                :id="labelId"
                v-bind="$attrs"
                ref="input"
                v-model="vm"
                :class="classes"
                :type="type"
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
const props = defineProps<{
    id?: string,
    desc?: string,
    label?: string,
    modelValue?: any,
    validity?: string,
    elementRef?: HTMLInputElement,
    rows?: number|string,
    type?: string,
    value?: string,
}>();
const emit = defineEmits(['update:modelValue', 'update:elementRef']);

const vm = useVModel(props, 'modelValue', emit);
const input = ref<HTMLInputElement>();
const err = useWatchValidation(vm, input);

watch(input, val => emit('update:elementRef', val));

watch(() => props.validity, val => {

    if (input.value) {
        if (val) {
            input.value.setCustomValidity(val);
        } else {
            input.value.setCustomValidity('');
        }
    }
});

const uniqueId = useGetUniqueId();
const labelId = computed(() => props.id || uniqueId);

const isCheckbox = computed(() => props.type == 'checkbox');

const classes = computed(() => ({'mw-input': true, 'input-error': !!err.value}));
</script>

<style>
.flex > .input-container:not(.flex-col > .input-container) {
    flex: 1;
}

.input-bg {
    flex: 1;
    color: var(--text-color);
    background-color: var(--input-bg-color);
    border: 1px solid var(--input-border-color);
    border-radius: var(--border-radius);
}

.mw-input {
    padding: 0.6rem;
    flex: 1;
    transition: border-color 0.25s;
    height: auto;
    color: var(--text-color);
    background-color: var(--input-bg-color);
    border: 1px solid var(--input-border-color);
    border-radius: var(--border-radius);
    resize: vertical;
}

.mw-input:focus-visible {
    outline: none;
    border-color: var(--primary-color)
}

.mw-input-invalid {
    border-color: var(--danger-color)
}

.mw-input[type='color'] {
    flex: 6;
}

.mw-input[type='checkbox'] {
    width: revert;
    margin-top: 3px;
}

.mw-input:disabled {
    opacity: 0.4;
}
</style>